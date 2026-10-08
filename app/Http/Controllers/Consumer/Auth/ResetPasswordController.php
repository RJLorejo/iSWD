<?php

namespace App\Http\Controllers\Consumer\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VerificationOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class ResetPasswordController extends Controller
{
    private const PURPOSE = 'password_reset';

    private const CHANNEL = 'email';

    private const SESSION_KEY =
        'consumer_password_reset';


    /**
     * Show new-password page.
     */
    public function create()
    {
        $reset = session(
            self::SESSION_KEY
        );


        /*
        |--------------------------------------------------------------------------
        | Reset Session Required
        |--------------------------------------------------------------------------
        */

        if (
            !$reset ||
            empty($reset['user_id']) ||
            empty($reset['email']) ||
            empty($reset['otp_id'])
        ) {
            return redirect()
                ->route(
                    'consumer.password.request'
                )
                ->withErrors([
                    'email' =>
                        'Please verify your email before resetting your password.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Session Expiration
        |--------------------------------------------------------------------------
        */

        if ($this->resetSessionExpired($reset)) {

            $this->clearResetSession();


            return redirect()
                ->route(
                    'consumer.password.request'
                )
                ->withErrors([
                    'email' =>
                        'Your password reset session has expired. Please start again.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | OTP Must Be Verified
        |--------------------------------------------------------------------------
        */

        if (
            ($reset['otp_verified'] ?? false) !== true
        ) {
            return redirect()
                ->route(
                    'consumer.password.verify'
                )
                ->withErrors([
                    'otp' =>
                        'Please verify the 6-digit code before creating a new password.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Verify OTP Record
        |--------------------------------------------------------------------------
        */

        $otp = $this->getVerifiedOtp(
            $reset
        );


        if (!$otp) {

            $this->clearResetSession();


            return redirect()
                ->route(
                    'consumer.password.request'
                )
                ->withErrors([
                    'email' =>
                        'Your verification is no longer valid. Please start again.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Find Consumer
        |--------------------------------------------------------------------------
        */

        $user = User::query()
            ->find(
                $reset['user_id']
            );


        if (
            !$user ||
            !$user->hasRole('Consumer') ||
            Str::lower($user->email) !==
                Str::lower($reset['email'])
        ) {

            $this->clearResetSession();


            return redirect()
                ->route(
                    'consumer.password.request'
                )
                ->withErrors([
                    'email' =>
                        'The password reset request is no longer valid.',
                ]);
        }


        return view(
            'consumer.auth.reset-password',
            [
                'email' =>
                    $user->email,
            ]
        );
    }


    /**
     * Save new password.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'password' => [
                    'required',
                    'confirmed',

                    Password::min(8)
                        ->mixedCase()
                        ->numbers(),
                ],
            ],
            [
                'password.required' =>
                    'Please enter your new password.',

                'password.confirmed' =>
                    'The password confirmation does not match.',
            ]
        );


        $reset = session(
            self::SESSION_KEY
        );


        /*
        |--------------------------------------------------------------------------
        | Reset Session Required
        |--------------------------------------------------------------------------
        */

        if (
            !$reset ||
            empty($reset['user_id']) ||
            empty($reset['email']) ||
            empty($reset['otp_id'])
        ) {
            return redirect()
                ->route(
                    'consumer.password.request'
                )
                ->withErrors([
                    'email' =>
                        'Your password reset session has expired. Please start again.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Session Expiration
        |--------------------------------------------------------------------------
        */

        if ($this->resetSessionExpired($reset)) {

            $this->clearResetSession();


            return redirect()
                ->route(
                    'consumer.password.request'
                )
                ->withErrors([
                    'email' =>
                        'Your password reset session has expired. Please start again.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | OTP Verification Required
        |--------------------------------------------------------------------------
        */

        if (
            ($reset['otp_verified'] ?? false) !== true
        ) {
            return redirect()
                ->route(
                    'consumer.password.verify'
                )
                ->withErrors([
                    'otp' =>
                        'Please verify the verification code first.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Verify OTP Database Record
        |--------------------------------------------------------------------------
        */

        $otp = $this->getVerifiedOtp(
            $reset
        );


        if (!$otp) {

            $this->clearResetSession();


            return redirect()
                ->route(
                    'consumer.password.request'
                )
                ->withErrors([
                    'email' =>
                        'Your verification is no longer valid. Please start again.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Find Consumer User
        |--------------------------------------------------------------------------
        */

        $user = User::query()
            ->find(
                $reset['user_id']
            );


        if (
            !$user ||
            !$user->hasRole('Consumer') ||
            Str::lower($user->email) !==
                Str::lower($reset['email'])
        ) {

            $this->clearResetSession();


            return redirect()
                ->route(
                    'consumer.password.request'
                )
                ->withErrors([
                    'email' =>
                        'The password reset request is no longer valid.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Update Password
        |--------------------------------------------------------------------------
        |
        | User model already has:
        |
        | 'password' => 'hashed'
        |
        | Therefore Laravel hashes this automatically.
        |
        */

        DB::transaction(
            function () use (
                $user,
                $validated
            ) {

                $user->update([
                    'password' =>
                        $validated['password'],

                    'remember_token' =>
                        Str::random(60),
                ]);


                /*
                |--------------------------------------------------------------------------
                | Invalidate Password Reset OTPs
                |--------------------------------------------------------------------------
                */

                VerificationOtp::query()
                    ->where(
                        'user_id',
                        $user->id
                    )
                    ->where(
                        'channel',
                        self::CHANNEL
                    )
                    ->where(
                        'purpose',
                        self::PURPOSE
                    )
                    ->delete();
            }
        );


        /*
        |--------------------------------------------------------------------------
        | Clear Session
        |--------------------------------------------------------------------------
        */

        $this->clearResetSession();


        /*
        |--------------------------------------------------------------------------
        | Return to Login
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'consumer.login'
            )
            ->with(
                'success',
                'Your password has been reset successfully. You can now sign in using your new password.'
            );
    }


    /**
     * Get successfully verified OTP.
     */
    private function getVerifiedOtp(
        array $reset
    ): ?VerificationOtp {

        return VerificationOtp::query()
            ->where(
                'id',
                $reset['otp_id']
            )
            ->where(
                'user_id',
                $reset['user_id']
            )
            ->where(
                'channel',
                self::CHANNEL
            )
            ->where(
                'purpose',
                self::PURPOSE
            )
            ->whereNotNull(
                'verified_at'
            )
            ->first();
    }


    /**
     * Check reset-session expiration.
     */
    private function resetSessionExpired(
        array $reset
    ): bool {

        if (
            empty($reset['expires_at'])
        ) {
            return true;
        }


        return now()->timestamp >
            (int) $reset['expires_at'];
    }


    /**
     * Clear reset session.
     */
    private function clearResetSession(): void
    {
        session()->forget(
            self::SESSION_KEY
        );
    }
}
