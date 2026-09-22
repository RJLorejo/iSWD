<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consumer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsumerVerificationController extends Controller
{
    /**
     * Display consumer registrations for verification.
     */
    public function index(Request $request)
    {
        $query = Consumer::query()
            ->with([
                'user',
                'address',
            ])
            ->where(
                'registration_source',
                'Self Registration'
            );

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim(
                $request->search
            );

            $query->where(
                function ($q) use ($search) {

                    $q->where(
                        'account_number',
                        'like',
                        "%{$search}%"
                    )
                        ->orWhere(
                            'first_name',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'middle_name',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'last_name',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'email',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'phone',
                            'like',
                            "%{$search}%"
                        );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Verification Status
        |--------------------------------------------------------------------------
        */

        $status = $request->get(
            'status',
            'Pending Verification'
        );

        if ($status !== 'All') {

            $query->where(
                'verification_status',
                $status
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Registrations
        |--------------------------------------------------------------------------
        */

        $consumers = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();


        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */

        $baseQuery = Consumer::query()
            ->where(
                'registration_source',
                'Self Registration'
            );


        $totalCount = (clone $baseQuery)
            ->count();


        $pendingCount = (clone $baseQuery)
            ->where(
                'verification_status',
                'Pending Verification'
            )
            ->count();


        $verifiedCount = (clone $baseQuery)
            ->where(
                'verification_status',
                'Verified'
            )
            ->count();


        $rejectedCount = (clone $baseQuery)
            ->where(
                'verification_status',
                'Rejected'
            )
            ->count();


        return view(
            'admin.consumer-verifications.index',
            compact(
                'consumers',
                'status',
                'totalCount',
                'pendingCount',
                'verifiedCount',
                'rejectedCount'
            )
        );
    }


    /**
     * Display a consumer registration.
     */
    public function show(Consumer $consumer)
    {
        /*
        |--------------------------------------------------------------------------
        | Only Self-Registered Consumers
        |--------------------------------------------------------------------------
        */

        if (
            $consumer->registration_source
            !== 'Self Registration'
        ) {
            abort(404);
        }


        /*
        |--------------------------------------------------------------------------
        | Load Registration Information
        |--------------------------------------------------------------------------
        */

        $consumer->load([
            'user',
            'address',
            'verifier',
        ]);


        return view(
            'admin.consumer-verifications.show',
            compact('consumer')
        );
    }


    /**
     * Approve consumer registration.
     */
    public function approve(Consumer $consumer)
    {
        /*
        |--------------------------------------------------------------------------
        | Validate Registration Source
        |--------------------------------------------------------------------------
        */

        if (
            $consumer->registration_source
            !== 'Self Registration'
        ) {
            abort(404);
        }


        /*
        |--------------------------------------------------------------------------
        | Only Pending Registrations Can Be Approved
        |--------------------------------------------------------------------------
        */

        if (
            $consumer->verification_status
            !== 'Pending Verification'
        ) {
            return back()->with(
                'error',
                'This consumer registration has already been reviewed.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Consumer Must Have Portal Account
        |--------------------------------------------------------------------------
        */

        $consumer->load('user');

        if (!$consumer->user) {

            return back()->with(
                'error',
                'This consumer does not have a linked portal account.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Approve Registration
        |--------------------------------------------------------------------------
        */

        DB::transaction(
            function () use ($consumer) {

                /*
                 * Update Consumer.
                 */

                $consumer->update([

                    'verification_status' =>
                    'Verified',

                    'verified_at' =>
                    now(),

                    'verified_by' =>
                    auth()->id(),

                    'verification_reason' =>
                    null,

                    'is_active' =>
                    true,
                ]);


                /*
                 * Activate Portal User.
                 */

                $consumer->user->update([
                    'is_active' => true,
                ]);
            }
        );


        return redirect()
            ->route(
                'admin.consumer-verifications.show',
                $consumer
            )
            ->with(
                'success',
                'Consumer registration approved successfully. The consumer can now sign in to the iSWD Consumer Portal.'
            );
    }


    /**
     * Reject consumer registration.
     */
    public function reject(
        Request $request,
        Consumer $consumer
    ) {
        /*
        |--------------------------------------------------------------------------
        | Validate Registration Source
        |--------------------------------------------------------------------------
        */

        if (
            $consumer->registration_source
            !== 'Self Registration'
        ) {
            abort(404);
        }


        /*
        |--------------------------------------------------------------------------
        | Only Pending Registrations Can Be Rejected
        |--------------------------------------------------------------------------
        */

        if (
            $consumer->verification_status
            !== 'Pending Verification'
        ) {
            return back()->with(
                'error',
                'This consumer registration has already been reviewed.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Validate Rejection Reason
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'verification_reason' => [
                'required',
                'string',
                'max:1000',
            ],
        ], [
            'verification_reason.required' =>
            'Please provide a reason for rejecting the registration.',

            'verification_reason.max' =>
            'The verification reason must not exceed 1000 characters.',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Reject Registration
        |--------------------------------------------------------------------------
        */

        DB::transaction(
            function () use (
                $consumer,
                $validated
            ) {

                /*
                 * Update Consumer.
                 */

                $consumer->update([

                    'verification_status' =>
                    'Rejected',

                    'verified_at' =>
                    now(),

                    'verified_by' =>
                    auth()->id(),

                    'verification_reason' =>
                    $validated['verification_reason'],

                    'is_active' =>
                    false,
                ]);


                /*
                 * Keep Portal User inactive.
                 */

                if ($consumer->user) {

                    $consumer->user->update([
                        'is_active' => false,
                    ]);
                }
            }
        );


        return redirect()
            ->route(
                'admin.consumer-verifications.show',
                $consumer
            )
            ->with(
                'success',
                'Consumer registration rejected successfully.'
            );
    }
}
