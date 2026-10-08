<?php

namespace App\Http\Controllers\Consumer\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Consumer\RegisterConsumerRequest;
use App\Mail\ConsumerEmailOtpMail;
use App\Models\VerificationOtp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | OTP Settings
    |--------------------------------------------------------------------------
    */

    private const OTP_EXPIRY_MINUTES = 5;


    public function create()
    {
        $pending = session(
            'consumer_pending_registration'
        );

        /*
    |--------------------------------------------------------------------------
    | Restore Pending Registration
    |--------------------------------------------------------------------------
    |
    | If the applicant returns from the OTP page, restore the registration
    | information into Laravel's old input so the existing registration Blade
    | continues working without redesigning it.
    |
    | Password fields are intentionally NOT restored.
    |
    */

        if ($pending) {

            session()->flashInput([

                'account_number' =>
                $pending['account_number'] ?? '',

                'first_name' =>
                $pending['first_name'] ?? '',

                'middle_name' =>
                $pending['middle_name'] ?? '',

                'last_name' =>
                $pending['last_name'] ?? '',

                'suffix' =>
                $pending['suffix'] ?? '',

                'sex' =>
                $pending['sex'] ?? '',

                'phone' =>
                $pending['phone'] ?? '',

                'email' =>
                $pending['email'] ?? '',

                'house_no' =>
                $pending['house_no'] ?? '',

                'street' =>
                $pending['street'] ?? '',

                'purok' =>
                $pending['purok'] ?? '',

                'barangay' =>
                $pending['barangay'] ?? '',

                'latitude' =>
                $pending['latitude'] ?? '',

                'longitude' =>
                $pending['longitude'] ?? '',

                /*
             * The applicant already accepted the terms
             * during the original submission.
             */
                'terms' => 1,
            ]);
        }


        return view(
            'consumer.auth.register'
        );
    }


    /**
     * Validate the registration form and send the email OTP.
     *
     * IMPORTANT:
     * No User, Consumer, or ConsumerAddress record is created here.
     * Permanent records are created only after successful OTP verification.
     */
    public function store(
        RegisterConsumerRequest $request
    ): RedirectResponse {

        /*
        |--------------------------------------------------------------------------
        | Validated Registration Data
        |--------------------------------------------------------------------------
        */

        $validated = $request->validated();


        /*
        |--------------------------------------------------------------------------
        | Never Store Plaintext Password In Session
        |--------------------------------------------------------------------------
        |
        | Hash the password now.
        |
        | The final account creation will use this already-hashed password.
        |
        */

        $passwordHash = Hash::make(
            $validated['password']
        );


        /*
        |--------------------------------------------------------------------------
        | Remove Fields We Do Not Need In Temporary Registration
        |--------------------------------------------------------------------------
        |
        | password_confirmation and terms do not need to be stored.
        |
        */

        unset(
            $validated['password'],
            $validated['password_confirmation'],
            $validated['terms']
        );


        /*
        |--------------------------------------------------------------------------
        | Temporary Registration
        |--------------------------------------------------------------------------
        |
        | The registration is stored only in the server-side session.
        |
        | Nothing is permanently inserted into users, consumers, or
        | consumer_addresses yet.
        |
        */

        $request->session()->put(
            'consumer_pending_registration',
            [
                ...$validated,

                /*
                 * Already hashed.
                 */
                'password_hash' => $passwordHash,

                /*
                 * Used to expire the whole temporary registration.
                 */
                'expires_at' => now()
                    ->addMinutes(15)
                    ->toIso8601String(),
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | Invalidate Previous Registration OTP For This Email
        |--------------------------------------------------------------------------
        |
        | If the applicant submitted the form again using the same email,
        | any previous active registration OTP should no longer be usable.
        |
        */

        VerificationOtp::query()
            ->where('channel', 'email')
            ->where('purpose', 'registration')
            ->where('destination', $validated['email'])
            ->whereNull('verified_at')
            ->delete();


        /*
        |--------------------------------------------------------------------------
        | Generate OTP
        |--------------------------------------------------------------------------
        */

        $code = (string) random_int(
            100000,
            999999
        );


        /*
        |--------------------------------------------------------------------------
        | Store OTP
        |--------------------------------------------------------------------------
        |
        | Only the hash of the OTP is stored.
        |
        */

        $otp = VerificationOtp::create([

            'user_id' => null,

            'channel' =>
            'email',

            'purpose' =>
            'registration',

            'destination' =>
            $validated['email'],

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
        | Send OTP Email
        |--------------------------------------------------------------------------
        |
        | If sending fails:
        |
        | - delete the OTP
        | - keep no pending registration
        | - return to the registration form with all normal old() inputs
        |
        */

        try {

            Mail::to(
                $validated['email']
            )->send(
                new ConsumerEmailOtpMail($code)
            );
        } catch (\Throwable $exception) {

            report($exception);

            /*
             * Remove OTP because it was never successfully delivered.
             */

            $otp->delete();


            /*
             * Remove pending registration.
             */

            $request->session()->forget(
                'consumer_pending_registration'
            );


            return back()
                ->withInput(
                    $request->except([
                        'password',
                        'password_confirmation',
                    ])
                )
                ->withErrors([
                    'email' =>
                    'We could not send the verification code to this email address. Please check your email and try again.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Proceed To OTP Verification
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'consumer.register.verify-email'
            );
    }
}
