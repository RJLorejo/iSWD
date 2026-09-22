<?php

namespace App\Http\Controllers\Consumer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Consumer\ResubmitConsumerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistrationStatusController extends Controller
{
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

        abort_unless(
            $consumer->verification_status === 'Rejected',
            403
        );

        return view(
            'consumer.registration.correct',
            compact('consumer')
        );
    }

    public function update(
        ResubmitConsumerRequest $request
    ) {
        $user = $request->user();

        $consumer = $user->consumer()
            ->with('address')
            ->firstOrFail();

        abort_unless(
            $consumer->verification_status === 'Rejected',
            403
        );

        $validated = $request->validated();

        DB::transaction(function () use (
            $validated,
            $consumer,
            $user
        ) {

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
                    ]
                );
        });

        return redirect()
            ->route('consumer.registration.status')
            ->with(
                'success',
                'Your corrected registration has been resubmitted successfully and is now pending verification by Sagay Water District.'
            );
    }
}
