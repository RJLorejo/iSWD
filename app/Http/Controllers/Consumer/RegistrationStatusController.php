<?php

namespace App\Http\Controllers\Consumer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Consumer\ResubmitConsumerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistrationStatusController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration Status
    |--------------------------------------------------------------------------
    */

    public function show(Request $request)
    {
        $user = $request->user();

        abort_unless(
            $user && $user->hasRole('Consumer'),
            403
        );


        $consumer = $user->consumer()
            ->with([
                'address',
                'verifier',
            ])
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Already Verified
        |--------------------------------------------------------------------------
        |
        | Once both the User and Consumer accounts are active,
        | the consumer no longer needs the registration status page.
        |
        */

        if (
            $consumer->verification_status === 'Verified' &&
            $consumer->is_active &&
            $user->is_active
        ) {
            return redirect()
                ->route('consumer.dashboard');
        }


        return view(
            'consumer.registration.status',
            compact('consumer')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Correct Rejected Registration
    |--------------------------------------------------------------------------
    */

    public function edit(Request $request)
    {
        $user = $request->user();

        abort_unless(
            $user && $user->hasRole('Consumer'),
            403
        );


        $consumer = $user->consumer()
            ->with('address')
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Only Rejected Registrations Can Be Corrected
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $consumer->verification_status === 'Rejected',
            403
        );


        return view(
            'consumer.registration.correct',
            compact('consumer')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Resubmit Corrected Registration
    |--------------------------------------------------------------------------
    */

    public function update(
        ResubmitConsumerRequest $request
    ) {
        $user = $request->user();


        $consumer = $user->consumer()
            ->with('address')
            ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Security Check
        |--------------------------------------------------------------------------
        |
        | The FormRequest already performs this authorization check,
        | but keeping this check here provides an additional safeguard.
        |
        */

        abort_unless(
            $consumer->verification_status === 'Rejected',
            403
        );


        $validated = $request->validated();


        /*
        |--------------------------------------------------------------------------
        | Update Registration
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $validated,
            $consumer,
            $user
        ) {

            /*
            |--------------------------------------------------------------------------
            | Consumer
            |--------------------------------------------------------------------------
            */

            $consumer->update([

                'account_number' =>
                    $validated['account_number'],

                'first_name' =>
                    $validated['first_name'],

                'middle_name' =>
                    $validated['middle_name'] ?? null,

                'last_name' =>
                    $validated['last_name'],

                'suffix' =>
                    $validated['suffix'] ?? null,

                'sex' =>
                    $validated['sex'],

                'phone' =>
                    $validated['phone'],


                /*
                |--------------------------------------------------------------------------
                | Return to Pending Verification
                |--------------------------------------------------------------------------
                */

                'verification_status' =>
                    'Pending Verification',

                'verified_at' =>
                    null,

                'verified_by' =>
                    null,

                'verification_reason' =>
                    null,

                'is_active' =>
                    false,
            ]);


            /*
            |--------------------------------------------------------------------------
            | User
            |--------------------------------------------------------------------------
            |
            | Keep User information synchronized with Consumer information.
            |
            */

            $user->update([

                'first_name' =>
                    $validated['first_name'],

                'middle_name' =>
                    $validated['middle_name'] ?? null,

                'last_name' =>
                    $validated['last_name'],

                'suffix' =>
                    $validated['suffix'] ?? null,

                'phone' =>
                    $validated['phone'],

                'is_active' =>
                    false,
            ]);


            /*
            |--------------------------------------------------------------------------
            | Consumer Address
            |--------------------------------------------------------------------------
            */

            $consumer->address()
                ->updateOrCreate(
                    [
                        'consumer_id' =>
                            $consumer->id,
                    ],
                    [
                        'house_no' =>
                            $validated['house_no'] ?? null,

                        'street' =>
                            $validated['street'] ?? null,

                        'purok' =>
                            $validated['purok'] ?? null,

                        'barangay' =>
                            $validated['barangay'],

                        'municipality' =>
                            'Sagay',

                        'province' =>
                            'Negros Occidental',

                        'latitude' =>
                            $validated['latitude'],

                        'longitude' =>
                            $validated['longitude'],
                    ]
                );
        });


        /*
        |--------------------------------------------------------------------------
        | Return to Registration Status
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('consumer.registration.status')
            ->with(
                'success',
                'Your corrected registration has been resubmitted successfully and is now pending verification by Sagay Water District.'
            );
    }
}
