<?php

namespace App\Http\Controllers\Consumer\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ConsumerEmailOtpMail;
use App\Models\Consumer;
use App\Models\ConsumerAddress;
use App\Models\User;
use App\Models\VerificationOtp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RegistrationOtpController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $registration = $request->session()->get('consumer_pending_registration');

        if (!$registration || empty($registration['email'])) {
            return redirect()
                ->route('consumer.register')
                ->withErrors(['email' => 'Your registration session expired. Please complete the form again.']);
        }

        return view('consumer.auth.verify-email', [
            'email' => $registration['email'],
        ]);
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ], [
            'otp.required' => 'Enter the 6-digit verification code.',
            'otp.digits' => 'The verification code must contain exactly 6 digits.',
        ]);

        $registration = $request->session()->get('consumer_pending_registration');
        $encryptedPassword = $request->session()->get('consumer_pending_registration_password');

        if (!$registration || !$encryptedPassword || empty($registration['email'])) {
            return redirect()
                ->route('consumer.register')
                ->withErrors(['email' => 'Your registration session expired. Please complete the form again.']);
        }

        $email = strtolower(trim($registration['email']));

        $verification = VerificationOtp::query()
            ->where('channel', 'email')
            ->where('purpose', 'registration')
            ->where('destination', $email)
            ->whereNull('verified_at')
            ->latest('id')
            ->first();

        if (!$verification) {
            return back()->withErrors(['otp' => 'No active verification code was found. Please request a new code.']);
        }

        if ($verification->expires_at->isPast()) {
            return back()->withErrors(['otp' => 'This verification code has expired. Please request a new code.']);
        }

        if ($verification->attempts >= 5) {
            return back()->withErrors(['otp' => 'Too many incorrect attempts. Please request a new verification code.']);
        }

        if (!Hash::check($request->otp, $verification->code_hash)) {
            $verification->increment('attempts');
            $verification->refresh();
            $remaining = max(0, 5 - $verification->attempts);

            return back()->withErrors([
                'otp' => $remaining > 0
                    ? "Incorrect verification code. {$remaining} attempt(s) remaining."
                    : 'Incorrect verification code. Please request a new code.',
            ]);
        }

        // Re-check uniqueness immediately before account creation.
        validator($registration, [
            'account_number' => ['required', Rule::unique('consumers', 'account_number')],
            'email' => ['required', Rule::unique('users', 'email')],
            'phone' => ['required', Rule::unique('users', 'phone')],
        ])->validate();

        $password = Crypt::decryptString($encryptedPassword);

        DB::transaction(function () use ($registration, $password, $verification) {
            $user = User::create([
                'employee_id' => null,
                'first_name' => $registration['first_name'],
                'middle_name' => $registration['middle_name'] ?? null,
                'last_name' => $registration['last_name'],
                'suffix' => $registration['suffix'] ?? null,
                'email' => $registration['email'],
                'email_verified_at' => now(),
                'phone' => $registration['phone'],
                'password' => $password,
                'is_active' => false,
            ]);

            $user->assignRole('Consumer');

            $consumer = Consumer::create([
                'account_number' => $registration['account_number'],
                'user_id' => $user->id,
                'first_name' => $registration['first_name'],
                'middle_name' => $registration['middle_name'] ?? null,
                'last_name' => $registration['last_name'],
                'suffix' => $registration['suffix'] ?? null,
                'sex' => $registration['sex'],
                'phone' => $registration['phone'],
                'email' => $registration['email'],
                'verification_status' => 'Pending Verification',
                'verified_at' => null,
                'verified_by' => null,
                'verification_reason' => null,
                'email_verified_at' => now(),
                'phone_verified_at' => null,
                'registration_source' => 'Self Registration',
                'is_active' => false,
            ]);

            ConsumerAddress::create([
                'consumer_id' => $consumer->id,
                'house_no' => $registration['house_no'],
                'street' => $registration['street'],
                'purok' => $registration['purok'],
                'barangay' => $registration['barangay'],
                'municipality' => 'Sagay',
                'province' => 'Negros Occidental',
                'latitude' => $registration['latitude'],
                'longitude' => $registration['longitude'],
            ]);

            $verification->update(['verified_at' => now()]);
        });

        $request->session()->forget([
            'consumer_pending_registration',
            'consumer_pending_registration_password',
        ]);

        return redirect()
            ->route('consumer.login')
            ->with(
                'success',
                'Email verified and registration submitted successfully. Your account is now awaiting verification by Sagay Water District.'
            );
    }

    public function resend(Request $request): RedirectResponse
    {
        $registration = $request->session()->get('consumer_pending_registration');

        if (!$registration || empty($registration['email'])) {
            return redirect()
                ->route('consumer.register')
                ->withErrors(['email' => 'Your registration session expired. Please complete the form again.']);
        }

        $email = strtolower(trim($registration['email']));

        $latest = VerificationOtp::query()
            ->where('channel', 'email')
            ->where('purpose', 'registration')
            ->where('destination', $email)
            ->latest('id')
            ->first();

        if ($latest?->last_sent_at && $latest->last_sent_at->gt(now()->subSeconds(60))) {
            $seconds = (int) ceil(now()->diffInSeconds($latest->last_sent_at->copy()->addSeconds(60), false));

            return back()->withErrors([
                'otp' => 'Please wait ' . max(1, $seconds) . ' second(s) before requesting another code.',
            ]);
        }

        VerificationOtp::query()
            ->where('channel', 'email')
            ->where('purpose', 'registration')
            ->where('destination', $email)
            ->whereNull('verified_at')
            ->delete();

        $otp = (string) random_int(100000, 999999);

        $verification = VerificationOtp::create([
            'user_id' => null,
            'channel' => 'email',
            'purpose' => 'registration',
            'destination' => $email,
            'code_hash' => Hash::make($otp),
            'expires_at' => now()->addMinutes(5),
            'attempts' => 0,
            'verified_at' => null,
            'last_sent_at' => now(),
        ]);

        try {
            Mail::to($email)->send(new ConsumerEmailOtpMail($otp));
        } catch (\Throwable $exception) {
            $verification->delete();
            report($exception);

            return back()->withErrors([
                'otp' => 'We could not resend the verification code. Please try again in a moment.',
            ]);
        }

        return back()->with('success', 'A new 6-digit verification code was sent to your email.');
    }
}
