<?php

namespace App\Http\Controllers\CustomerService;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintCategory;
use App\Models\Division;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ComplaintVerificationController extends Controller
{
    /**
     * Display complaints waiting for Customer Service verification.
     */
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Verification Queue
        |--------------------------------------------------------------------------
        |
        | ONLY Pending complaints.
        |
        | Verified, Assigned, In Progress, Completed, Closed and Rejected
        | complaints remain available from All Complaints.
        |
        */

        $query = Complaint::query()
            ->with([
                'consumer',
                'category',
                'division',
                'customerService',
                'verifier',
                'technicians',
                'aiAnalysis',
            ])
            ->where(
                'status',
                'Pending'
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
                function (Builder $query) use ($search) {

                    $query
                        ->where(
                            'complaint_no',
                            'like',
                            "%{$search}%"
                        )

                        ->orWhere(
                            'description',
                            'like',
                            "%{$search}%"
                        )

                        ->orWhere(
                            'address',
                            'like',
                            "%{$search}%"
                        )

                        ->orWhere(
                            'landmark',
                            'like',
                            "%{$search}%"
                        )

                        ->orWhere(
                            'complainant_name',
                            'like',
                            "%{$search}%"
                        )

                        ->orWhere(
                            'complainant_phone',
                            'like',
                            "%{$search}%"
                        )

                        /*
                        |--------------------------------------------------------------------------
                        | Consumer
                        |--------------------------------------------------------------------------
                        */

                        ->orWhereHas(
                            'consumer',
                            function (Builder $consumer) use ($search) {

                                $consumer
                                    ->where(
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
                                        'account_number',
                                        'like',
                                        "%{$search}%"
                                    )

                                    ->orWhere(
                                        'phone',
                                        'like',
                                        "%{$search}%"
                                    );
                            }
                        )

                        /*
                        |--------------------------------------------------------------------------
                        | Complaint Type
                        |--------------------------------------------------------------------------
                        */

                        ->orWhereHas(
                            'category',
                            function (Builder $category) use ($search) {

                                $category
                                    ->where(
                                        'name',
                                        'like',
                                        "%{$search}%"
                                    )

                                    ->orWhere(
                                        'code',
                                        'like',
                                        "%{$search}%"
                                    );
                            }
                        )

                        /*
                        |--------------------------------------------------------------------------
                        | Division
                        |--------------------------------------------------------------------------
                        */

                        ->orWhereHas(
                            'division',
                            function (Builder $division) use ($search) {

                                $division->where(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                );
                            }
                        );
                }
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Urgency
        |--------------------------------------------------------------------------
        */

        if ($request->filled('urgency')) {

            $urgency = $request->urgency;

            if (
                in_array(
                    $urgency,
                    [
                        'High',
                        'Moderate',
                        'Low',
                    ],
                    true
                )
            ) {

                $query->whereHas(
                    'aiAnalysis',
                    function (Builder $aiQuery) use ($urgency) {

                        $aiQuery->where(
                            'urgency_level',
                            $urgency
                        );
                    }
                );
            } elseif (
                $urgency === 'not_assessed'
            ) {

                $query->where(
                    function (Builder $urgencyQuery) {

                        $urgencyQuery
                            ->whereDoesntHave(
                                'aiAnalysis'
                            )

                            ->orWhereHas(
                                'aiAnalysis',
                                function (Builder $aiQuery) {

                                    $aiQuery
                                        ->whereNull(
                                            'urgency_level'
                                        )

                                        ->orWhere(
                                            'urgency_level',
                                            ''
                                        );
                                }
                            );
                    }
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Complaint Type
        |--------------------------------------------------------------------------
        */

        if ($request->filled('complaint_type')) {

            $query->where(
                'complaint_category_id',
                $request->complaint_type
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Division
        |--------------------------------------------------------------------------
        */

        if ($request->filled('division_id')) {

            $query->where(
                'division_id',
                $request->division_id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Date From
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date_from')) {

            $query->whereDate(
                'created_at',
                '>=',
                $request->date_from
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Date To
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date_to')) {

            $query->whereDate(
                'created_at',
                '<=',
                $request->date_to
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Pending Complaints
        |--------------------------------------------------------------------------
        */

        $pendingComplaints = $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Minimal Statistic
        |--------------------------------------------------------------------------
        */

        $pendingCount = Complaint::query()
            ->where(
                'status',
                'Pending'
            )
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Filter Options
        |--------------------------------------------------------------------------
        */

        $divisions = Division::query()
            ->where(
                'is_active',
                true
            )
            ->orderBy('name')
            ->get();


        $complaintTypes = ComplaintCategory::query()
            ->where(
                'is_active',
                true
            )
            ->orderBy('name')
            ->get();


        return view(
            'customer-service.complaint-verification.index',
            compact(
                'pendingComplaints',
                'pendingCount',
                'divisions',
                'complaintTypes'
            )
        );
    }
}
