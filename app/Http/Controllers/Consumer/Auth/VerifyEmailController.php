<?php

namespace App\Http\Controllers\Consumer\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ConsumerEmailOtpMail;
use App\Models\Consumer;
use App\Models\User;
use App\Models\VerificationOtp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class VerifyEmailController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | OTP Settings
    |--------------------------------------------------------------------------
    */

    private const MAX_ATTEMPTS = 5;

    private const RESEND_SECONDS = 60;

    private const OTP_EXPIRY_MINUTES = 5;

    private const REGISTRATION_EXPIRY_MINUTES = 15;


    /**
     * Show OTP verification page.
     */
    public function show(
        Request $request
    ): View|RedirectResponse {

        /*
        |--------------------------------------------------------------------------
        | Get Pending Registration
        |--------------------------------------------------------------------------
        */

        $pending = $request
            ->session()
            ->get(
                'consumer_pending_registration'
            );


        /*
        |--------------------------------------------------------------------------
        | No Pending Registration
        |--------------------------------------------------------------------------
        */

        if (! $pending) {

            return $this->startOver(
                'Your registration session expired. Please register again.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Check Temporary Registration Lifetime
        |--------------------------------------------------------------------------
        */

        if ($this->registrationExpired($pending)) {

            $this->clearPendingRegistration(
                $request
            );

            return $this->startOver(
                'Your registration session expired. Please register again.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Get Current OTP
        |--------------------------------------------------------------------------
        */

        $otp = $this->currentOtp(
            $pending['email']
        );


        /*
        |--------------------------------------------------------------------------
        | OTP Page
        |--------------------------------------------------------------------------
        */

        return view(
            'consumer.auth.verify-email',
            [
                'maskedEmail' =>
                $this->maskEmail(
                    $pending['email']
                ),

                'resendIn' =>
                $otp
                    ? $this->secondsUntilResend($otp)
                    : 0,
            ]
        );
    }


    /**
     * Verify OTP and complete registration.
     */
    public function verify(
        Request $request
    ): RedirectResponse {

        /*
        |--------------------------------------------------------------------------
        | Validate OTP Input
        |--------------------------------------------------------------------------
        */

        $request->validate(
            [
                'code' => [
                    'required',
                    'digits:6',
                ],
            ],
            [
                'code.required' =>
                'Enter the 6-digit verification code.',

                'code.digits' =>
                'The verification code must be exactly 6 digits.',
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | Pending Registration
        |--------------------------------------------------------------------------
        */

        $pending = $request
            ->session()
            ->get(
                'consumer_pending_registration'
            );


        if (! $pending) {

            return $this->startOver(
                'Your registration session expired. Please register again.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Check Registration Session Expiry
        |--------------------------------------------------------------------------
        */

        if ($this->registrationExpired($pending)) {

            $this->clearPendingRegistration(
                $request
            );

            return $this->startOver(
                'Your registration session expired. Please register again.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Find Active OTP
        |--------------------------------------------------------------------------
        */

        $otp = $this->currentOtp(
            $pending['email']
        );


        if (! $otp) {

            return back()
                ->withErrors([
                    'code' =>
                    'No active verification code was found. Please request a new code.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Check OTP Expiration
        |--------------------------------------------------------------------------
        */

        if (
            Carbon::parse(
                $otp->expires_at
            )->isPast()
        ) {

            return back()
                ->withErrors([
                    'code' =>
                    'This verification code has expired. Please request a new code.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Maximum Attempts
        |--------------------------------------------------------------------------
        */

        if (
            $otp->attempts
            >= self::MAX_ATTEMPTS
        ) {

            return back()
                ->withErrors([
                    'code' =>
                    'Too many incorrect attempts. Please request a new verification code.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Verify OTP
        |--------------------------------------------------------------------------
        */

        if (
            ! Hash::check(
                $request->input('code'),
                $otp->code_hash
            )
        ) {

            $otp->increment(
                'attempts'
            );

            /*
             * Refresh the model so attempts contains
             * the latest database value.
             */

            $otp->refresh();

            $attemptsLeft = max(
                0,
                self::MAX_ATTEMPTS
                    - $otp->attempts
            );


            if ($attemptsLeft <= 0) {

                return back()
                    ->withErrors([
                        'code' =>
                        'Too many incorrect attempts. Please request a new verification code.',
                    ]);
            }


            return back()
                ->withErrors([
                    'code' =>
                    'Incorrect verification code. '
                        . $attemptsLeft
                        . ' attempt'
                        . ($attemptsLeft === 1 ? '' : 's')
                        . ' remaining.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Recheck Duplicate Email
        |--------------------------------------------------------------------------
        |
        | The registration form checked this earlier.
        |
        | But another registration could theoretically have created the same
        | email while this applicant was on the OTP page.
        |
        */

        if (
            User::withTrashed()
            ->where(
                'email',
                $pending['email']
            )
            ->exists()
        ) {

            $this->clearPendingRegistration(
                $request
            );

            return $this->startOver(
                'This email address is already registered. Please use another email address or sign in to your existing account.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Recheck SWD Account Number
        |--------------------------------------------------------------------------
        */

        if (
            Consumer::withTrashed()
            ->where(
                'account_number',
                $pending['account_number']
            )
            ->exists()
        ) {

            $this->clearPendingRegistration(
                $request
            );

            return $this->startOver(
                'This Sagay Water District account number is already registered.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Create Permanent Registration
        |--------------------------------------------------------------------------
        */

        /*
|--------------------------------------------------------------------------
| Recheck Mobile Number
|--------------------------------------------------------------------------
|
| Another registration may have completed while this applicant
| was still on the OTP verification page.
|
*/

        if (
            Consumer::withTrashed()
            ->where(
                'phone',
                $pending['phone']
            )
            ->exists()
        ) {

            $this->clearPendingRegistration(
                $request
            );

            return $this->startOver(
                'This mobile number is already registered. Please use another mobile number.'
            );
        }

        try {

            DB::transaction(
                function () use (
                    $otp,
                    $pending
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | Create User + Consumer + Address
                    |--------------------------------------------------------------------------
                    */

                    $this->createAccount(
                        $pending
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | Mark OTP Verified
                    |--------------------------------------------------------------------------
                    |
                    | Do this inside the same transaction.
                    |
                    */

                    $otp->update([
                        'verified_at' =>
                        now(),
                    ]);
                }
            );
        } catch (\Throwable $exception) {

            report($exception);

            return back()
                ->withErrors([
                    'code' =>
                    'Your verification code was correct, but we could not finish creating your registration. Please try again.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Remove Temporary Registration
        |--------------------------------------------------------------------------
        */

        $this->clearPendingRegistration(
            $request
        );


        /*
        |--------------------------------------------------------------------------
        | Registration Successfully Submitted
        |--------------------------------------------------------------------------
        |
        | Email is verified.
        |
        | SWD verification is still pending.
        |
        */

        return redirect()
            ->route(
                'consumer.login'
            )
            ->with(
                'success',
                'Email verified successfully. Your registration has been submitted and is now pending verification by Sagay Water District.'
            );
    }


    /**
     * Resend OTP.
     */
    public function resend(
        Request $request
    ): RedirectResponse {

        /*
        |--------------------------------------------------------------------------
        | Pending Registration
        |--------------------------------------------------------------------------
        */

        $pending = $request
            ->session()
            ->get(
                'consumer_pending_registration'
            );


        if (! $pending) {

            return $this->startOver(
                'Your registration session expired. Please register again.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Registration Session Expired
        |--------------------------------------------------------------------------
        */

        if ($this->registrationExpired($pending)) {

            $this->clearPendingRegistration(
                $request
            );

            return $this->startOver(
                'Your registration session expired. Please register again.'
            );
        }


        $email = $pending['email'];


        /*
        |--------------------------------------------------------------------------
        | Existing OTP
        |--------------------------------------------------------------------------
        */

        $otp = $this->currentOtp(
            $email
        );


        /*
        |--------------------------------------------------------------------------
        | Resend Cooldown
        |--------------------------------------------------------------------------
        */

        if (
            $otp &&
            $this->secondsUntilResend($otp) > 0
        ) {

            $seconds = $this
                ->secondsUntilResend($otp);


            return back()
                ->withErrors([
                    'code' =>
                    'Please wait '
                        . $seconds
                        . ' second'
                        . ($seconds === 1 ? '' : 's')
                        . ' before requesting a new code.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Generate New OTP
        |--------------------------------------------------------------------------
        */

        $code = (string) random_int(
            100000,
            999999
        );


        /*
        |--------------------------------------------------------------------------
        | Prepare New OTP Data
        |--------------------------------------------------------------------------
        */

        $attributes = [

            'code_hash' =>
            Hash::make($code),

            'expires_at' =>
            now()->addMinutes(
                self::OTP_EXPIRY_MINUTES
            ),

            /*
             * New code = new attempt counter.
             */

            'attempts' =>
            0,

            'verified_at' =>
            null,

            'last_sent_at' =>
            now(),
        ];


        /*
        |--------------------------------------------------------------------------
        | Create Or Replace OTP
        |--------------------------------------------------------------------------
        |
        | Updating the existing record automatically invalidates
        | the previous code because its hash is replaced.
        |
        */

        if ($otp) {

            /*
             * Keep a copy so we can restore it if email sending fails.
             */

            $oldOtp = [
                'code_hash' =>
                $otp->code_hash,

                'expires_at' =>
                $otp->expires_at,

                'attempts' =>
                $otp->attempts,

                'verified_at' =>
                $otp->verified_at,

                'last_sent_at' =>
                $otp->last_sent_at,
            ];


            $otp->update(
                $attributes
            );
        } else {

            $oldOtp = null;


            $otp = VerificationOtp::create(
                $attributes + [

                    'user_id' =>
                    null,

                    'channel' =>
                    'email',

                    'purpose' =>
                    'registration',

                    'destination' =>
                    $email,
                ]
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Send New OTP
        |--------------------------------------------------------------------------
        */

        try {

            Mail::to(
                $email
            )->send(
                new ConsumerEmailOtpMail(
                    $code
                )
            );
        } catch (\Throwable $exception) {

            report($exception);


            /*
             * Restore the previous OTP if one existed.
             *
             * Otherwise remove the newly created OTP.
             */

            if ($oldOtp !== null) {

                $otp->update(
                    $oldOtp
                );
            } else {

                $otp->delete();
            }


            return back()
                ->withErrors([
                    'code' =>
                    'We could not send a new verification code. Please try again in a moment.',
                ]);
        }


        return back()
            ->with(
                'success',
                'A new 6-digit verification code was sent to your email.'
            );
    }

    /**
     * Change the pending registration email address.
     *
     * The User/Consumer account does not exist yet.
     * We only update the temporary registration session,
     * invalidate the previous OTP, and send a new OTP.
     */
    public function changeEmail(
        Request $request
    ): RedirectResponse {

        /*
    |--------------------------------------------------------------------------
    | Validate New Email
    |--------------------------------------------------------------------------
    */

        $validated = $request->validate(
            [
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    'unique:users,email',
                ],
            ],
            [
                'email.required' =>
                'Enter your new email address.',

                'email.email' =>
                'Enter a valid email address.',

                'email.max' =>
                'The email address is too long.',

                'email.unique' =>
                'This email address is already registered.',
            ]
        );


        /*
    |--------------------------------------------------------------------------
    | Pending Registration
    |--------------------------------------------------------------------------
    */

        $pending = $request
            ->session()
            ->get(
                'consumer_pending_registration'
            );


        if (! $pending) {

            return $this->startOver(
                'Your registration session expired. Please register again.'
            );
        }


        /*
    |--------------------------------------------------------------------------
    | Registration Session Expired
    |--------------------------------------------------------------------------
    */

        if (
            $this->registrationExpired(
                $pending
            )
        ) {

            $this->clearPendingRegistration(
                $request
            );


            return $this->startOver(
                'Your registration session expired. Please register again.'
            );
        }


        $oldEmail =
            $pending['email'];

        $newEmail =
            strtolower(
                trim(
                    $validated['email']
                )
            );


        /*
    |--------------------------------------------------------------------------
    | Same Email
    |--------------------------------------------------------------------------
    */

        if (
            strtolower($oldEmail)
            === $newEmail
        ) {

            return back()
                ->withErrors([
                    'email' =>
                    'This is already the email address used for your registration.',
                ]);
        }


        /*
    |--------------------------------------------------------------------------
    | Generate New OTP
    |--------------------------------------------------------------------------
    */

        $code = (string) random_int(
            100000,
            999999
        );


        /*
    |--------------------------------------------------------------------------
    | Create OTP For New Email
    |--------------------------------------------------------------------------
    |
    | Do NOT invalidate the old OTP yet.
    |
    | We first make sure the verification email can actually be sent.
    |
    */

        $newOtp = VerificationOtp::create([

            'user_id' =>
            null,

            'channel' =>
            'email',

            'purpose' =>
            'registration',

            'destination' =>
            $newEmail,

            'code_hash' =>
            Hash::make($code),

            'expires_at' =>
            now()->addMinutes(
                self::OTP_EXPIRY_MINUTES
            ),

            'attempts' =>
            0,

            'verified_at' =>
            null,

            'last_sent_at' =>
            now(),
        ]);


        /*
    |--------------------------------------------------------------------------
    | Send OTP To New Email
    |--------------------------------------------------------------------------
    */

        try {

            Mail::to(
                $newEmail
            )->send(
                new ConsumerEmailOtpMail(
                    $code
                )
            );
        } catch (\Throwable $exception) {

            report($exception);


            /*
         * The new email could not receive the OTP.
         *
         * Delete the new OTP and leave the previous email/session untouched.
         */

            $newOtp->delete();


            return back()
                ->withErrors([
                    'email' =>
                    'We could not send a verification code to the new email address. Your previous email has not been changed.',
                ]);
        }


        /*
    |--------------------------------------------------------------------------
    | Invalidate Old Email OTP
    |--------------------------------------------------------------------------
    |
    | New OTP was successfully sent, so the previous OTP must no longer work.
    |
    */

        VerificationOtp::query()

            ->where(
                'channel',
                'email'
            )

            ->where(
                'purpose',
                'registration'
            )

            ->where(
                'destination',
                $oldEmail
            )

            ->whereNull(
                'verified_at'
            )

            ->delete();


        /*
    |--------------------------------------------------------------------------
    | Update Pending Registration Email
    |--------------------------------------------------------------------------
    */

        $pending['email'] =
            $newEmail;


        $request
            ->session()
            ->put(
                'consumer_pending_registration',
                $pending
            );


        /*
    |--------------------------------------------------------------------------
    | Success
    |--------------------------------------------------------------------------
    */

        return redirect()
            ->route(
                'consumer.register.verify-email'
            )
            ->with([
                'success' =>
                'Your email address was changed. A new 6-digit verification code was sent to your new email.',

                'focus_otp' =>
                true,
            ]);
    }
    /*
    |--------------------------------------------------------------------------
    | Current Registration OTP
    |--------------------------------------------------------------------------
    */

    private function currentOtp(
        string $email
    ): ?VerificationOtp {

        return VerificationOtp::query()

            ->where(
                'channel',
                'email'
            )

            ->where(
                'purpose',
                'registration'
            )

            ->where(
                'destination',
                $email
            )

            ->whereNull(
                'verified_at'
            )

            ->latest('id')

            ->first();
    }


    /*
    |--------------------------------------------------------------------------
    | Resend Cooldown
    |--------------------------------------------------------------------------
    */

    private function secondsUntilResend(
        VerificationOtp $otp
    ): int {

        if (! $otp->last_sent_at) {
            return 0;
        }


        $lastSent = Carbon::parse(
            $otp->last_sent_at
        );


        $elapsed =
            now()->timestamp
            - $lastSent->timestamp;


        return max(
            0,
            self::RESEND_SECONDS
                - $elapsed
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Registration Session Expiration
    |--------------------------------------------------------------------------
    */

    private function registrationExpired(
        array $pending
    ): bool {

        if (
            empty($pending['expires_at'])
        ) {
            return true;
        }


        return Carbon::parse(
            $pending['expires_at']
        )->isPast();
    }


    /*
    |--------------------------------------------------------------------------
    | Mask Email
    |--------------------------------------------------------------------------
    */

    private function maskEmail(
        string $email
    ): string {

        [$name, $domain] =
            array_pad(
                explode(
                    '@',
                    $email,
                    2
                ),
                2,
                ''
            );


        /*
         * Handle very short email usernames.
         */

        if (
            mb_strlen($name) <= 2
        ) {

            $maskedName =
                mb_substr(
                    $name,
                    0,
                    1
                )
                . '*';
        } else {

            $maskedName =
                mb_substr(
                    $name,
                    0,
                    2
                )
                . str_repeat(
                    '*',
                    max(
                        1,
                        mb_strlen($name) - 2
                    )
                );
        }


        return $maskedName
            . '@'
            . $domain;
    }


    /*
    |--------------------------------------------------------------------------
    | Start Registration Again
    |--------------------------------------------------------------------------
    */

    private function startOver(
        string $message
    ): RedirectResponse {

        return redirect()
            ->route(
                'consumer.register'
            )
            ->withErrors([
                'email' =>
                $message,
            ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Clear Temporary Registration
    |--------------------------------------------------------------------------
    */

    private function clearPendingRegistration(
        Request $request
    ): void {

        $request
            ->session()
            ->forget(
                'consumer_pending_registration'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Create Permanent Consumer Registration
    |--------------------------------------------------------------------------
    |
    | This method runs ONLY after the email OTP has been successfully verified.
    |
    */

    private function createAccount(
        array $data
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Create User Account
        |--------------------------------------------------------------------------
        */

        $user = User::create([

            'employee_id' =>
            null,

            'first_name' =>
            $data['first_name'],

            'middle_name' =>
            $data['middle_name']
                ?? null,

            'last_name' =>
            $data['last_name'],

            'suffix' =>
            $data['suffix']
                ?? null,

            'email' =>
            $data['email'],

            /*
             * OTP has successfully verified the email.
             */

            'email_verified_at' =>
            now(),

            'phone' =>
            $data['phone'],

            /*
             * IMPORTANT:
             *
             * password_hash is already hashed.
             *
             * We use setRawAttributes below after creation? No.
             * Instead create the User first without passing this through
             * the "hashed" cast twice.
             */

            'password' =>
            $data['password_hash'],

            /*
             * SWD has NOT approved the account yet.
             */

            'is_active' =>
            false,
        ]);


        /*
        |--------------------------------------------------------------------------
        | Assign Consumer Role
        |--------------------------------------------------------------------------
        |
        | Your existing system uses exactly:
        |
        | Consumer
        |
        */

        $user->assignRole(
            'Consumer'
        );


        /*
        |--------------------------------------------------------------------------
        | Create Consumer Record
        |--------------------------------------------------------------------------
        */

        $consumer = Consumer::create([

            /*
            |--------------------------------------------------------------------------
            | Water Account
            |--------------------------------------------------------------------------
            */

            'account_number' =>
            $data['account_number'],

            'user_id' =>
            $user->id,


            /*
            |--------------------------------------------------------------------------
            | Personal Information
            |--------------------------------------------------------------------------
            */

            'first_name' =>
            $data['first_name'],

            'middle_name' =>
            $data['middle_name']
                ?? null,

            'last_name' =>
            $data['last_name'],

            'suffix' =>
            $data['suffix']
                ?? null,

            'sex' =>
            $data['sex'],


            /*
            |--------------------------------------------------------------------------
            | Contact
            |--------------------------------------------------------------------------
            */

            'phone' =>
            $data['phone'],

            'email' =>
            $data['email'],


            /*
            |--------------------------------------------------------------------------
            | SWD Verification
            |--------------------------------------------------------------------------
            |
            | Email verification and SWD verification are different.
            |
            */

            'verification_status' =>
            'Pending Verification',

            'verified_at' =>
            null,

            'verified_by' =>
            null,

            'verification_reason' =>
            null,


            /*
            |--------------------------------------------------------------------------
            | Email / Phone Verification
            |--------------------------------------------------------------------------
            */

            'email_verified_at' =>
            now(),

            'phone_verified_at' =>
            null,


            /*
            |--------------------------------------------------------------------------
            | Registration Source
            |--------------------------------------------------------------------------
            |
            | Preserve the value used by your existing registration system.
            |
            */

            'registration_source' =>
            'Self Registration',


            /*
            |--------------------------------------------------------------------------
            | Account Status
            |--------------------------------------------------------------------------
            |
            | Email is verified, but portal access stays disabled
            | until SWD approves the registration.
            |
            */

            'is_active' =>
            false,
        ]);


        /*
        |--------------------------------------------------------------------------
        | Create Consumer Service Address
        |--------------------------------------------------------------------------
        */

        $consumer
            ->address()
            ->create([

                'house_no' =>
                $data['house_no']
                    ?? null,

                'street' =>
                $data['street']
                    ?? null,

                'purok' =>
                $data['purok']
                    ?? null,

                'barangay' =>
                $data['barangay'],

                /*
                 * Preserve your existing database values.
                 */

                'municipality' =>
                'Sagay',

                'province' =>
                'Negros Occidental',

                /*
                 * Registered water service location.
                 */

                'latitude' =>
                $data['latitude'],

                'longitude' =>
                $data['longitude'],
            ]);
    }
}
