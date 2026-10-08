<?php
namespace App\Http\Controllers\Consumer\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ConsumerPasswordResetOtpMail;
use App\Models\User;
use App\Models\VerificationOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Throwable;

class ForgotPasswordController extends Controller
{
    private const PURPOSE = 'password_reset';
    private const CHANNEL = 'email';
    private const SESSION_KEY = 'consumer_password_reset';

    private const OTP_EXPIRY_MINUTES = 5;
    private const MAX_ATTEMPTS = 5;
    private const RESEND_COOLDOWN_SECONDS = 60;
    private const RESET_SESSION_MINUTES = 15;

    // Separate from the 5 OTP verification attempts.
    private const EMAIL_REQUEST_LIMIT = 10;
    private const EMAIL_REQUEST_WINDOW = 3600;

    private const GENERIC_MESSAGE =
        'If a Consumer Portal account exists for this email address, a 6-digit verification code has been sent.';

    private const LOCKED_MESSAGE =
        'Too many incorrect attempts. Your verification code is now locked. Please request a new code.';

    /*
    |--------------------------------------------------------------------------
    | Forgot Password Page
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('consumer.auth.forgot-password');
    }

    /*
    |--------------------------------------------------------------------------
    | Submit Email
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'email' => ['required', 'email', 'max:255'],
            ],
            [
                'email.required' => 'Please enter your registered email address.',
                'email.email' => 'Please enter a valid email address.',
            ]
        );

        $email = Str::lower(trim($validated['email']));
        $now = now();

        // A fresh recovery session is created for every valid email format.
        $this->clearResetSession();

        $reset = [
            'email' => $email,
            'user_id' => null,
            'otp_id' => null,
            'otp_verified' => false,
            'attempts' => 0,

            'otp_expires_at' => $now->copy()
                ->addMinutes(self::OTP_EXPIRY_MINUTES)
                ->timestamp,

            'resend_available_at' => $now->copy()
                ->addSeconds(self::RESEND_COOLDOWN_SECONDS)
                ->timestamp,

            'expires_at' => $now->copy()
                ->addMinutes(self::RESET_SESSION_MINUTES)
                ->timestamp,
        ];

        $rateKey = $this->emailRateKey($email);

        $canSend = !RateLimiter::tooManyAttempts(
            $rateKey,
            self::EMAIL_REQUEST_LIMIT
        );

        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        if ($user && $user->hasRole('Consumer')) {
            $latestOtp = $this->latestOtpForUser($user);

            $cooldownRemaining = $this->databaseCooldownRemaining(
                $latestOtp
            );

            if ($canSend && $cooldownRemaining === 0) {
                $sent = $this->sendNewOtp($user, $reset);

                if ($sent) {
                    RateLimiter::hit(
                        $rateKey,
                        self::EMAIL_REQUEST_WINDOW
                    );
                } else {
                    // Preserve an older valid OTP when email delivery fails.
                    $this->attachExistingOtp(
                        $user,
                        $latestOtp,
                        $reset
                    );
                }
            } else {
                // Do not send again during cooldown or rate limiting.
                // Preserve the real OTP's remaining attempts.
                $this->attachExistingOtp(
                    $user,
                    $latestOtp,
                    $reset
                );
            }
        } else {
            // Unknown email: same visible recovery flow, no email sent.
            if ($canSend) {
                RateLimiter::hit(
                    $rateKey,
                    self::EMAIL_REQUEST_WINDOW
                );
            }
        }

        session([
            self::SESSION_KEY => $reset,
        ]);

        return redirect()
            ->route('consumer.password.verify')
            ->with('status', self::GENERIC_MESSAGE);
    }

    /*
    |--------------------------------------------------------------------------
    | Show OTP Verification Page
    |--------------------------------------------------------------------------
    */

    public function showVerifyOtp()
    {
        $reset = session(self::SESSION_KEY);

        if (!$this->validResetSession($reset)) {
            $this->clearResetSession();

            return redirect()
                ->route('consumer.password.request')
                ->withErrors([
                    'email' => 'Your password reset session has expired. Please start again.',
                ]);
        }

        $otp = $this->getCurrentOtp($reset);

        if (
            ($reset['otp_verified'] ?? false) === true &&
            $otp &&
            $otp->isVerified()
        ) {
            return redirect()
                ->route('consumer.password.reset');
        }

        $attemptsUsed = $this->attemptsUsed($reset, $otp);

        $attemptsRemaining = max(
            0,
            self::MAX_ATTEMPTS - $attemptsUsed
        );

        $tooManyAttempts = $attemptsRemaining === 0;

        // The Blade hides the expiration timer when locked.
        $otpExpiresIn = $tooManyAttempts
            ? 0
            : $this->otpSecondsRemaining($reset);

        $resendAvailableIn = max(
            0,
            (int) ($reset['resend_available_at'] ?? 0)
                - now()->timestamp
        );

        return view('consumer.auth.verify-reset-otp', [
            'email' => $reset['email'],
            'resendAvailableIn' => $resendAvailableIn,
            'otpExpiresIn' => $otpExpiresIn,
            'attemptsRemaining' => $attemptsRemaining,
            'tooManyAttempts' => $tooManyAttempts,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Verify Submitted OTP
    |--------------------------------------------------------------------------
    */

    public function verifyOtp(Request $request)
    {
        $validated = $request->validate(
            [
                'otp' => ['required', 'digits:6'],
            ],
            [
                'otp.required' => 'Please enter the 6-digit verification code.',
                'otp.digits' => 'The verification code must contain exactly 6 digits.',
            ]
        );

        $reset = session(self::SESSION_KEY);

        if (!$this->validResetSession($reset)) {
            $this->clearResetSession();

            return redirect()
                ->route('consumer.password.request')
                ->withErrors([
                    'email' => 'Your password reset session has expired. Please start again.',
                ]);
        }

        $otp = $this->getCurrentOtp($reset);

        if (
            ($reset['otp_verified'] ?? false) === true &&
            $otp &&
            $otp->isVerified()
        ) {
            return redirect()
                ->route('consumer.password.reset');
        }

        /*
        |--------------------------------------------------------------------------
        | Check Attempts BEFORE Checking the Code
        |--------------------------------------------------------------------------
        */

        $attemptsUsed = $this->attemptsUsed($reset, $otp);

        if ($attemptsUsed >= self::MAX_ATTEMPTS) {
            return back()
                ->withErrors([
                    'otp' => self::LOCKED_MESSAGE,
                ])
                ->with('clear_otp', true);
        }

        /*
        |--------------------------------------------------------------------------
        | Check Expiration
        |--------------------------------------------------------------------------
        */

        if ($this->otpSecondsRemaining($reset) <= 0) {
            return back()
                ->withErrors([
                    'otp' => 'This verification code has expired. Please request a new code.',
                ])
                ->with('clear_otp', true);
        }

        /*
        |--------------------------------------------------------------------------
        | Validate Actual OTP
        |--------------------------------------------------------------------------
        */

        $validOtp = false;

        if (
            $otp &&
            !$otp->isVerified() &&
            !$otp->isExpired() &&
            (int) $otp->attempts < self::MAX_ATTEMPTS
        ) {
            $user = User::query()
                ->find($reset['user_id']);

            if (
                $user &&
                $user->hasRole('Consumer') &&
                Str::lower($user->email) ===
                    Str::lower($reset['email'])
            ) {
                $validOtp = Hash::check(
                    $validated['otp'],
                    $otp->code_hash
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Incorrect OTP
        |--------------------------------------------------------------------------
        */

        if (!$validOtp) {
            // Increment exactly once per incorrect submission.
            if ($otp && !$otp->isVerified()) {
                $otp->increment('attempts');

                $attemptsUsed = min(
                    self::MAX_ATTEMPTS,
                    (int) $otp->fresh()->attempts
                );
            } else {
                // Fake recovery sessions also get exactly 5 attempts.
                $attemptsUsed = min(
                    self::MAX_ATTEMPTS,
                    $attemptsUsed + 1
                );
            }

            $reset['attempts'] = $attemptsUsed;

            session([
                self::SESSION_KEY => $reset,
            ]);

            $remaining = max(
                0,
                self::MAX_ATTEMPTS - $attemptsUsed
            );

            if ($remaining === 0) {
                return back()
                    ->withErrors([
                        'otp' => self::LOCKED_MESSAGE,
                    ])
                    ->with('clear_otp', true);
            }

            return back()
                ->withErrors([
                    'otp' => 'Incorrect verification code. You have '
                        . $remaining
                        . ' attempt'
                        . ($remaining === 1 ? '' : 's')
                        . ' remaining.',
                ])
                ->with('clear_otp', true);
        }

        /*
        |--------------------------------------------------------------------------
        | Correct OTP
        |--------------------------------------------------------------------------
        */

        $otp->update([
            'verified_at' => now(),
        ]);

        $reset['otp_verified'] = true;
        $reset['verified_at'] = now()->timestamp;

        session([
            self::SESSION_KEY => $reset,
        ]);

        $request->session()->regenerate();

        return redirect()
            ->route('consumer.password.reset')
            ->with(
                'status',
                'Email verified successfully. You can now create a new password.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Resend OTP
    |--------------------------------------------------------------------------
    */

    public function resendOtp()
    {
        $reset = session(self::SESSION_KEY);

        if (!$this->validResetSession($reset)) {
            $this->clearResetSession();

            return redirect()
                ->route('consumer.password.request')
                ->withErrors([
                    'email' => 'Your password reset session has expired. Please start again.',
                ]);
        }

        // A verified recovery session must proceed to password creation.
        if (($reset['otp_verified'] ?? false) === true) {
            return redirect()
                ->route('consumer.password.reset');
        }

        /*
        |--------------------------------------------------------------------------
        | Enforce 60-Second Cooldown
        |--------------------------------------------------------------------------
        */

        $remaining = max(
            0,
            (int) ($reset['resend_available_at'] ?? 0)
                - now()->timestamp
        );

        if ($remaining > 0) {
            return back()
                ->withErrors([
                    'otp' => 'Please wait '
                        . $remaining
                        . ' seconds before requesting another code.',
                ])
                ->with('clear_otp', true);
        }

        $rateKey = $this->emailRateKey($reset['email']);

        $canSend = !RateLimiter::tooManyAttempts(
            $rateKey,
            self::EMAIL_REQUEST_LIMIT
        );

        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [$reset['email']])
            ->first();

        $isEligible = $user && $user->hasRole('Consumer');

        $sent = false;

        if ($isEligible) {
            $latestOtp = $this->latestOtpForUser($user);

            $databaseCooldown = $this->databaseCooldownRemaining(
                $latestOtp
            );

            if ($canSend && $databaseCooldown === 0) {
                $sent = $this->sendNewOtp($user, $reset);

                if ($sent) {
                    RateLimiter::hit(
                        $rateKey,
                        self::EMAIL_REQUEST_WINDOW
                    );
                }
            }

            if (!$sent) {
                // Keep the current code and its failed-attempt count.
                $this->attachExistingOtp(
                    $user,
                    $latestOtp,
                    $reset
                );
            }
        } else {
            // Fake email: maintain the same UI flow.
            if ($canSend) {
                RateLimiter::hit(
                    $rateKey,
                    self::EMAIL_REQUEST_WINDOW
                );

                $this->resetFakeOtpState($reset);
                $sent = true;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Only Reset Attempts When a New Code Was Issued
        |--------------------------------------------------------------------------
        */

        if ($sent) {
            $reset['attempts'] = 0;
            $reset['otp_verified'] = false;

            $reset['resend_available_at'] = now()
                ->addSeconds(self::RESEND_COOLDOWN_SECONDS)
                ->timestamp;

            $reset['expires_at'] = now()
                ->addMinutes(self::RESET_SESSION_MINUTES)
                ->timestamp;

            unset($reset['verified_at']);
        }

        session([
            self::SESSION_KEY => $reset,
        ]);

        return redirect()
            ->route('consumer.password.verify')
            ->with('status', self::GENERIC_MESSAGE)
            ->with('clear_otp', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Generate and Send a New OTP
    |--------------------------------------------------------------------------
    */

    private function sendNewOtp(User $user, array &$reset): bool
    {
        $plainOtp = (string) random_int(100000, 999999);
        $now = now();

        $expiresAt = $now->copy()
            ->addMinutes(self::OTP_EXPIRY_MINUTES);

        $newOtp = VerificationOtp::create([
            'user_id' => $user->id,
            'channel' => self::CHANNEL,
            'purpose' => self::PURPOSE,
            'destination' => $user->email,
            'code_hash' => Hash::make($plainOtp),
            'expires_at' => $expiresAt,
            'attempts' => 0,
            'verified_at' => null,
            'last_sent_at' => $now,
        ]);

        try {
            Mail::to($user->email)->send(
                new ConsumerPasswordResetOtpMail($plainOtp)
            );

            // Invalidate older codes after successful delivery.
            VerificationOtp::query()
                ->where('user_id', $user->id)
                ->where('channel', self::CHANNEL)
                ->where('purpose', self::PURPOSE)
                ->where('id', '!=', $newOtp->id)
                ->delete();

            $reset['user_id'] = $user->id;
            $reset['otp_id'] = $newOtp->id;
            $reset['otp_verified'] = false;
            $reset['attempts'] = 0;

            $reset['otp_expires_at'] = $expiresAt->timestamp;

            $reset['resend_available_at'] = $now->copy()
                ->addSeconds(self::RESEND_COOLDOWN_SECONDS)
                ->timestamp;

            return true;
        } catch (Throwable $exception) {
            $newOtp->delete();

            report($exception);

            return false;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Attach an Existing OTP Without Resetting Attempts
    |--------------------------------------------------------------------------
    */

    private function attachExistingOtp(
        User $user,
        ?VerificationOtp $otp,
        array &$reset
    ): void {
        if (!$otp || $otp->isVerified()) {
            return;
        }

        $reset['user_id'] = $user->id;
        $reset['otp_id'] = $otp->id;
        $reset['otp_verified'] = false;

        $reset['attempts'] = min(
            self::MAX_ATTEMPTS,
            (int) $otp->attempts
        );

        $reset['otp_expires_at'] = $otp->expires_at->timestamp;

        if ($otp->last_sent_at) {
            $reset['resend_available_at'] = $otp->last_sent_at
                ->copy()
                ->addSeconds(self::RESEND_COOLDOWN_SECONDS)
                ->timestamp;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Latest OTP
    |--------------------------------------------------------------------------
    */

    private function latestOtpForUser(User $user): ?VerificationOtp
    {
        return VerificationOtp::query()
            ->where('user_id', $user->id)
            ->where('channel', self::CHANNEL)
            ->where('purpose', self::PURPOSE)
            ->orderByDesc('last_sent_at')
            ->orderByDesc('id')
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Database Cooldown
    |--------------------------------------------------------------------------
    */

    private function databaseCooldownRemaining(
        ?VerificationOtp $otp
    ): int {
        if (!$otp || !$otp->last_sent_at) {
            return 0;
        }

        return max(
            0,
            $otp->last_sent_at->copy()
                ->addSeconds(self::RESEND_COOLDOWN_SECONDS)
                ->timestamp - now()->timestamp
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Fake Email OTP State
    |--------------------------------------------------------------------------
    */

    private function resetFakeOtpState(array &$reset): void
    {
        $reset['user_id'] = null;
        $reset['otp_id'] = null;
        $reset['attempts'] = 0;
        $reset['otp_verified'] = false;

        $reset['otp_expires_at'] = now()
            ->addMinutes(self::OTP_EXPIRY_MINUTES)
            ->timestamp;
    }

    /*
    |--------------------------------------------------------------------------
    | Actual Attempts Used
    |--------------------------------------------------------------------------
    */

    private function attemptsUsed(
        array $reset,
        ?VerificationOtp $otp
    ): int {
        $used = max(
            0,
            (int) ($reset['attempts'] ?? 0)
        );

        if ($otp && !$otp->isVerified()) {
            $used = max(
                $used,
                (int) $otp->attempts
            );
        }

        return min(self::MAX_ATTEMPTS, $used);
    }

    /*
    |--------------------------------------------------------------------------
    | Get Current OTP
    |--------------------------------------------------------------------------
    */

    private function getCurrentOtp(
        array $reset
    ): ?VerificationOtp {
        if (
            empty($reset['user_id']) ||
            empty($reset['otp_id'])
        ) {
            return null;
        }

        return VerificationOtp::query()
            ->where('id', $reset['otp_id'])
            ->where('user_id', $reset['user_id'])
            ->where('channel', self::CHANNEL)
            ->where('purpose', self::PURPOSE)
            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Remaining OTP Lifetime
    |--------------------------------------------------------------------------
    */

    private function otpSecondsRemaining(array $reset): int
    {
        return max(
            0,
            (int) ($reset['otp_expires_at'] ?? 0)
                - now()->timestamp
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Validate Recovery Session
    |--------------------------------------------------------------------------
    */

    private function validResetSession($reset): bool
    {
        return is_array($reset)
            && !empty($reset['email'])
            && now()->timestamp <
                (int) ($reset['expires_at'] ?? 0);
    }

    /*
    |--------------------------------------------------------------------------
    | Email Rate Limiter Key
    |--------------------------------------------------------------------------
    */

    private function emailRateKey(string $email): string
    {
        return 'consumer-password-reset:'
            . hash('sha256', Str::lower($email));
    }

    /*
    |--------------------------------------------------------------------------
    | Clear Recovery Session
    |--------------------------------------------------------------------------
    */

    private function clearResetSession(): void
    {
        session()->forget(self::SESSION_KEY);
    }
}
