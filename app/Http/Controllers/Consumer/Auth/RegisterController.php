<?php

namespace App\Http\Controllers\Consumer\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Consumer\RegisterConsumerRequest;
use App\Models\Consumer;
use App\Models\ConsumerAddress;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    /**
     * Show consumer registration page.
     */
    public function create()
    {
        return view('consumer.auth.register');
    }


    /**
     * Store consumer self-registration.
     */
    public function store(
        RegisterConsumerRequest $request
    ) {
        /*
        |--------------------------------------------------------------------------
        | Validated Registration Data
        |--------------------------------------------------------------------------
        */

        $validated = $request->validated();


        /*
        |--------------------------------------------------------------------------
        | Create Registration
        |--------------------------------------------------------------------------
        |
        | Everything is wrapped inside one database transaction.
        |
        | If User, Consumer, Role, or Address creation fails,
        | Laravel rolls everything back.
        |
        */

        DB::transaction(function () use ($validated) {

            /*
            |--------------------------------------------------------------------------
            | Create User Account
            |--------------------------------------------------------------------------
            |
            | Self-registered consumers are created as INACTIVE.
            |
            | They cannot access the Consumer Portal until their registration
            | has been verified and approved by Sagay Water District.
            |
            */

            $user = User::create([

                'employee_id' => null,

                'first_name' =>
                    $validated['first_name'],

                'middle_name' =>
                    $validated['middle_name']
                    ?? null,

                'last_name' =>
                    $validated['last_name'],

                'suffix' =>
                    $validated['suffix']
                    ?? null,

                'email' =>
                    $validated['email'],

                'phone' =>
                    $validated['phone'],

                /*
                 * User model has:
                 *
                 * 'password' => 'hashed'
                 *
                 * Laravel automatically hashes this value.
                 */

                'password' =>
                    $validated['password'],

                /*
                 * Pending registrations must remain inactive.
                 */

                'is_active' => false,
            ]);


            /*
            |--------------------------------------------------------------------------
            | Assign Consumer Role
            |--------------------------------------------------------------------------
            */

            $user->assignRole('Consumer');


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
                    $validated['account_number'],

                'user_id' =>
                    $user->id,


                /*
                |--------------------------------------------------------------------------
                | Personal Information
                |--------------------------------------------------------------------------
                */

                'first_name' =>
                    $validated['first_name'],

                'middle_name' =>
                    $validated['middle_name']
                    ?? null,

                'last_name' =>
                    $validated['last_name'],

                'suffix' =>
                    $validated['suffix']
                    ?? null,

                'sex' =>
                    $validated['sex'],


                /*
                |--------------------------------------------------------------------------
                | Contact Information
                |--------------------------------------------------------------------------
                */

                'phone' =>
                    $validated['phone'],

                'email' =>
                    $validated['email'],


                /*
                |--------------------------------------------------------------------------
                | Verification Status
                |--------------------------------------------------------------------------
                |
                | IMPORTANT:
                |
                | These values match your EXISTING database.
                |
                | Your database uses:
                |
                | verification_status
                | verified_at
                | verified_by
                | verification_reason
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
                    null,

                'phone_verified_at' =>
                    null,


                /*
                |--------------------------------------------------------------------------
                | Registration Source
                |--------------------------------------------------------------------------
                */

                'registration_source' =>
                    'Self Registration',


                /*
                |--------------------------------------------------------------------------
                | Account Status
                |--------------------------------------------------------------------------
                |
                | Remains inactive until Admin approval.
                |
                */

                'is_active' =>
                    false,
            ]);


            /*
            |--------------------------------------------------------------------------
            | Create Service Address
            |--------------------------------------------------------------------------
            */

            ConsumerAddress::create([

                'consumer_id' =>
                    $consumer->id,

                'house_no' =>
                    $validated['house_no']
                    ?? null,

                'street' =>
                    $validated['street']
                    ?? null,

                'purok' =>
                    $validated['purok']
                    ?? null,

                'barangay' =>
                    $validated['barangay'],

                'municipality' =>
                    'Sagay',

                'province' =>
                    'Negros Occidental',
            ]);
        });


        /*
        |--------------------------------------------------------------------------
        | Registration Complete
        |--------------------------------------------------------------------------
        |
        | The registration was successfully submitted, but the consumer
        | account is NOT active yet.
        |
        */

        return redirect()
            ->route('consumer.login')
            ->with(
                'success',
                'Registration submitted successfully. Your account is pending verification by Sagay Water District. You will receive an update after your registration has been reviewed.'
            );
    }
}
