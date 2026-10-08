<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\StaffPasswordResetOtpMail;
use App\Models\User;
use App\Models\VerificationOtp;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Throwable;

class StaffPasswordRecoveryController extends Controller
{
    private const PURPOSE = 'staff_password_reset';
    private const CHANNEL = 'email';
    private const SESSION_KEY = 'staff_password_reset';

    private const OTP_MINUTES = 5;
    private const SESSION_MINUTES = 15;
    private const MAX_ATTEMPTS = 5;
    private const RESEND_SECONDS = 60;

    private const STAFF_ROLES = [
        'Administrator',
        'Customer Service',
        'Maintenance Manager',
        'Maintenance Technician',
    ];

    private const GENERIC_MESSAGE =
        'If an eligible employee account exists, a 6-digit verification code has been sent to its email address.';

    public function create()
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $email = Str::lower(trim($data['email']));
        $now = now();

        $request->session()->forget(self::SESSION_KEY);

        $recovery = [
            'email' => $email,
            'user_id' => null,
            'otp_id' => null,
            'otp_verified' => false,
            'attempts' => 0,
            'otp_expires_at' => $now->copy()->addMinutes(self::OTP_MINUTES)->timestamp,
            'expires_at' => $now->copy()->addMinutes(self::SESSION_MINUTES)->timestamp,
            'resend_available_at' => $now->copy()->addSeconds(self::RESEND_SECONDS)->timestamp,
        ];

        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        if ($this->eligible($user)) {
            $otp = $this->sendOtp($user);

            if ($otp) {
                $recovery['user_id'] = $user->id;
                $recovery['otp_id'] = $otp->id;
            }
        }

        $request->session()->put(self::SESSION_KEY, $recovery);

        return redirect()
            ->route('password.verify')
            ->with('status', self::GENERIC_MESSAGE);
    }

    public function showVerify()
    {
        $recovery = $this->recovery();

        if (!$recovery) {
            return $this->expiredRedirect();
        }

        if ($this->verified($recovery)) {
            return redirect()->route('password.reset');
        }

        return view('auth.verify-reset-otp', [
            'email' => $recovery['email'],
            'resendAvailableIn' => max(
                0,
                $recovery['resend_available_at'] - now()->timestamp
            ),
            'otpExpiresIn' => max(
                0,
                $recovery['otp_expires_at'] - now()->timestamp
            ),
            'attemptsRemaining' => max(
                0,
                self::MAX_ATTEMPTS - $recovery['attempts']
            ),
        ]);
    }

    public function verify(Request $request)
    {
        $data = $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $recovery = $this->recovery();

        if (!$recovery) {
            return $this->expiredRedirect();
        }

        if ($this->verified($recovery)) {
            return redirect()->route('password.reset');
        }

        if ($recovery['attempts'] >= self::MAX_ATTEMPTS) {
            return back()
                ->withErrors(['otp' => 'Too many incorrect attempts. Request a new code.'])
                ->with('clear_otp', true);
        }

        if (now()->timestamp >= $recovery['otp_expires_at']) {
            return back()
                ->withErrors(['otp' => 'Your verification code has expired. Request a new code.'])
                ->with('clear_otp', true);
        }

        $otp = $this->currentOtp($recovery);

        $valid = $otp
            && !$otp->isVerified()
            && !$otp->isExpired()
            && $otp->attempts < self::MAX_ATTEMPTS
            && Hash::check($data['otp'], $otp->code_hash);

        if (!$valid) {
            $recovery['attempts']++;

            $request->session()->put(self::SESSION_KEY, $recovery);

            if ($otp && !$otp->isVerified()) {
                $otp->increment('attempts');
            }

            return back()
                ->withErrors([
                    'otp' => $recovery['attempts'] >= self::MAX_ATTEMPTS
                        ? 'Too many incorrect attempts. Request a new code.'
                        : 'Invalid verification code. Please try again.',
                ])
                ->with('clear_otp', true);
        }

        $otp->update(['verified_at' => now()]);

        $recovery['otp_verified'] = true;
        $recovery['verified_at'] = now()->timestamp;

        $request->session()->put(self::SESSION_KEY, $recovery);
        $request->session()->regenerate();

        return redirect()
            ->route('password.reset')
            ->with('status', 'Email verified successfully. Create your new password.');
    }

    public function resend(Request $request)
    {
        $recovery = $this->recovery();

        if (!$recovery) {
            return $this->expiredRedirect();
        }

        if ($this->verified($recovery)) {
            return redirect()->route('password.reset');
        }

        $remaining = $recovery['resend_available_at'] - now()->timestamp;

        if ($remaining > 0) {
            return back()->withErrors([
                'otp' => "Please wait {$remaining} seconds before requesting another code.",
            ]);
        }

        $user = !empty($recovery['user_id'])
            ? User::find($recovery['user_id'])
            : null;

        $newOtp = null;

        if (
            $this->eligible($user)
            && Str::lower($user->email) === $recovery['email']
        ) {
            $newOtp = $this->sendOtp($user);
        }

        $now = now();

        $recovery['otp_id'] = $newOtp?->id;
        $recovery['otp_verified'] = false;
        $recovery['attempts'] = 0;
        $recovery['otp_expires_at'] = $now->copy()->addMinutes(self::OTP_MINUTES)->timestamp;
        $recovery['expires_at'] = $now->copy()->addMinutes(self::SESSION_MINUTES)->timestamp;
        $recovery['resend_available_at'] = $now->copy()->addSeconds(self::RESEND_SECONDS)->timestamp;

        unset($recovery['verified_at']);

        $request->session()->put(self::SESSION_KEY, $recovery);

        return redirect()
            ->route('password.verify')
            ->with('status', self::GENERIC_MESSAGE)
            ->with('clear_otp', true);
    }

    public function showReset()
    {
        $recovery = $this->recovery();

        if (!$recovery) {
            return $this->expiredRedirect();
        }

        if (!$this->verified($recovery)) {
            return redirect()->route('password.verify');
        }

        return view('auth.reset-password', [
            'email' => $recovery['email'],
        ]);
    }

    public function reset(Request $request)
    {
        $recovery = $this->recovery();

        if (!$recovery) {
            return $this->expiredRedirect();
        }

        if (!$this->verified($recovery)) {
            return redirect()->route('password.verify');
        }

        $request->validate([
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers(),
            ],
        ]);

        $user = User::find($recovery['user_id'] ?? null);

        if (
            !$this->eligible($user)
            || Str::lower($user->email) !== $recovery['email']
        ) {
            $request->session()->forget(self::SESSION_KEY);

            return redirect()->route('password.request')
                ->withErrors(['email' => 'Please restart password recovery.']);
        }

        DB::transaction(function () use ($user, $request, $recovery) {

            $otp = VerificationOtp::query()
                ->whereKey($recovery['otp_id'])
                ->where('user_id', $user->id)
                ->where('purpose', self::PURPOSE)
                ->where('channel', self::CHANNEL)
                ->lockForUpdate()
                ->first();

            if (
                !$otp
                || !$otp->isVerified()
                || now()->timestamp >= $recovery['expires_at']
            ) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'password' => 'Your verification session has expired. Please restart recovery.',
                ]);
            }

            $user->forceFill([
                'password' => Hash::make($request->password),
                'remember_token' => Str::random(60),
            ])->save();

            VerificationOtp::query()
                ->where('user_id', $user->id)
                ->where('purpose', self::PURPOSE)
                ->delete();

            event(new PasswordReset($user));
        });

        $request->session()->forget(self::SESSION_KEY);
        $request->session()->regenerate();

        return redirect()
            ->route('login')
            ->with('status', 'Your password has been reset successfully. Please sign in.');
    }

    private function eligible(?User $user): bool
    {
        return $user !== null
            && (bool) $user->is_active
            && $user->hasAnyRole(self::STAFF_ROLES);
    }

    private function sendOtp(User $user): ?VerificationOtp
    {
        $code = (string) random_int(100000, 999999);
        $now = now();

        $otp = VerificationOtp::create([
            'user_id' => $user->id,
            'channel' => self::CHANNEL,
            'purpose' => self::PURPOSE,
            'destination' => $user->email,
            'code_hash' => Hash::make($code),
            'expires_at' => $now->copy()->addMinutes(self::OTP_MINUTES),
            'attempts' => 0,
            'verified_at' => null,
            'last_sent_at' => $now,
        ]);

        try {
            Mail::to($user->email)
                ->send(new StaffPasswordResetOtpMail($code));

            VerificationOtp::query()
                ->where('user_id', $user->id)
                ->where('purpose', self::PURPOSE)
                ->where('channel', self::CHANNEL)
                ->where('id', '!=', $otp->id)
                ->delete();

            return $otp;
        } catch (Throwable $exception) {
            $otp->delete();
            report($exception);

            return null;
        }
    }

    private function recovery(): ?array
    {
        $recovery = session(self::SESSION_KEY);

        if (
            !is_array($recovery)
            || now()->timestamp >= (int) ($recovery['expires_at'] ?? 0)
        ) {
            session()->forget(self::SESSION_KEY);

            return null;
        }

        return $recovery;
    }

    private function currentOtp(array $recovery): ?VerificationOtp
    {
        if (empty($recovery['user_id']) || empty($recovery['otp_id'])) {
            return null;
        }

        return VerificationOtp::query()
            ->whereKey($recovery['otp_id'])
            ->where('user_id', $recovery['user_id'])
            ->where('purpose', self::PURPOSE)
            ->where('channel', self::CHANNEL)
            ->first();
    }

    private function verified(array $recovery): bool
    {
        if (empty($recovery['otp_verified'])) {
            return false;
        }

        $otp = $this->currentOtp($recovery);

        return $otp?->isVerified() === true;
    }

    private function expiredRedirect()
    {
        session()->forget(self::SESSION_KEY);

        return redirect()
            ->route('password.request')
            ->withErrors([
                'email' => 'Your recovery session has expired. Please start again.',
            ]);
    }
}
