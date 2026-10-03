<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    /**
     * Display complaints assigned to the logged-in technician.
     */
    public function index(Request $request)
    {
        $technicianId = Auth::id();

        /*
        |--------------------------------------------------------------------------
        | Scope
        |--------------------------------------------------------------------------
        |
        | active = Assigned + In Progress
        | all    = Assigned + In Progress + Completed + Closed
        |
        | Active is the default because these are complaints that still require
        | work from the technician.
        |
        */

        $scope = $request->get('scope', 'active');

        if (!in_array($scope, ['active', 'all'], true)) {
            $scope = 'active';
        }


        /*
        |--------------------------------------------------------------------------
        | Base Query
        |--------------------------------------------------------------------------
        */

        $query = $this->complaintsQuery($technicianId);


        /*
        |--------------------------------------------------------------------------
        | Active / All Scope
        |--------------------------------------------------------------------------
        */

        if ($scope === 'active') {

            $query->whereIn('status', [
                'Assigned',
                'In Progress',
            ]);
        } else {

            $query->whereIn('status', [
                'Assigned',
                'In Progress',
                'Completed',
                'Closed',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        |
        | Search:
        | - complaint number
        | - description
        | - address
        | - landmark
        | - consumer name
        | - account number
        | - phone
        | - complaint type/category
        |
        | Division is intentionally NOT included.
        |
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function (Builder $query) use ($search) {

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
                    );
            });
        }


        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            $allowedStatuses = [
                'Assigned',
                'In Progress',
                'Completed',
                'Closed',
            ];

            if (
                in_array(
                    $request->status,
                    $allowedStatuses,
                    true
                )
            ) {

                $query->where(
                    'status',
                    $request->status
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | AI Urgency Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('urgency')) {

            $urgency = strtoupper(
                trim($request->urgency)
            );

            if (
                in_array(
                    $urgency,
                    ['HIGH', 'MODERATE', 'LOW'],
                    true
                )
            ) {

                $query->whereHas(
                    'aiAnalysis',
                    function (Builder $aiQuery) use ($urgency) {

                        $aiQuery->whereRaw(
                            'UPPER(urgency_level) = ?',
                            [$urgency]
                        );
                    }
                );
            } elseif ($request->urgency === 'not_assessed') {

                $query->where(function (Builder $urgencyQuery) {

                    $urgencyQuery
                        ->whereDoesntHave('aiAnalysis')

                        ->orWhereHas(
                            'aiAnalysis',
                            function (Builder $aiQuery) {

                                $aiQuery
                                    ->whereNull('urgency_level')

                                    ->orWhere(
                                        'urgency_level',
                                        ''
                                    );
                            }
                        );
                });
            }
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

        $query
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        $complaints = $query
            ->paginate(10)
            ->withQueryString();

        $assignedCount = $this
            ->assignedComplaintsQuery($technicianId)
            ->where('status', 'Assigned')
            ->count();


        $inProgressCount = $this
            ->assignedComplaintsQuery($technicianId)
            ->where('status', 'In Progress')
            ->count();


        $urgentCount = $this
            ->assignedComplaintsQuery($technicianId)

            ->whereIn('status', [
                'Assigned',
                'In Progress',
            ])

            ->whereHas(
                'aiAnalysis',
                function (Builder $query) {

                    $query->whereRaw(
                        'UPPER(urgency_level) = ?',
                        ['HIGH']
                    );
                }
            )

            ->count();

        $activeCount = $assignedCount + $inProgressCount;


        return view(
            'technician.complaints.index',
            compact(
                'complaints',
                'assignedCount',
                'inProgressCount',
                'urgentCount',
                'activeCount',
                'scope'
            )
        );
    }


    /**
     * Print all complaints matching the current filters.
     *
     * This intentionally does not paginate.
     */
    public function printReport(Request $request)
    {
        $technicianId = Auth::id();

        $scope = $request->get('scope', 'active');

        if (!in_array($scope, ['active', 'all'], true)) {
            $scope = 'active';
        }


        /*
        |--------------------------------------------------------------------------
        | Base Query
        |--------------------------------------------------------------------------
        */

        $query = $this->complaintsQuery($technicianId);


        /*
        |--------------------------------------------------------------------------
        | Scope
        |--------------------------------------------------------------------------
        */

        if ($scope === 'active') {

            $query->whereIn('status', [
                'Assigned',
                'In Progress',
            ]);
        } else {

            $query->whereIn('status', [
                'Assigned',
                'In Progress',
                'Completed',
                'Closed',
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function (Builder $query) use ($search) {

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
                    );
            });
        }


        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            $allowedStatuses = [
                'Assigned',
                'In Progress',
                'Completed',
                'Closed',
            ];

            if (
                in_array(
                    $request->status,
                    $allowedStatuses,
                    true
                )
            ) {

                $query->where(
                    'status',
                    $request->status
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Urgency
        |--------------------------------------------------------------------------
        */

        if ($request->filled('urgency')) {

            $urgency = strtoupper(
                trim($request->urgency)
            );

            if (
                in_array(
                    $urgency,
                    ['HIGH', 'MODERATE', 'LOW'],
                    true
                )
            ) {

                $query->whereHas(
                    'aiAnalysis',
                    function (Builder $aiQuery) use ($urgency) {

                        $aiQuery->whereRaw(
                            'UPPER(urgency_level) = ?',
                            [$urgency]
                        );
                    }
                );
            } elseif ($request->urgency === 'not_assessed') {

                $query->where(function (Builder $urgencyQuery) {

                    $urgencyQuery
                        ->whereDoesntHave('aiAnalysis')

                        ->orWhereHas(
                            'aiAnalysis',
                            function (Builder $aiQuery) {

                                $aiQuery
                                    ->whereNull('urgency_level')

                                    ->orWhere(
                                        'urgency_level',
                                        ''
                                    );
                            }
                        );
                });
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Date Range
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date_from')) {

            $query->whereDate(
                'created_at',
                '>=',
                $request->date_from
            );
        }


        if ($request->filled('date_to')) {

            $query->whereDate(
                'created_at',
                '<=',
                $request->date_to
            );
        }
        $complaints = $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();


        return view(
            'technician.complaints.print-report',
            compact(
                'complaints',
                'scope'
            )
        );
    }


    /**
     * Display one assigned complaint.
     */
    public function show(Complaint $complaint)
    {
        $isAssignedToMe = $complaint
            ->technicians()
            ->where(
                'users.id',
                Auth::id()
            )
            ->exists();

        abort_unless(
            $isAssignedToMe,
            403
        );


        $complaint->load([
            'consumer',
            'division',
            'category',
            'customerService',
            'verifier',
            'technicians',
            'maintenanceReport',
            'aiAnalysis',
        ]);


        return view(
            'technician.complaints.show',
            compact('complaint')
        );
    }


    /**
     * Base query for the Technician complaint list.
     */
    private function complaintsQuery(int $technicianId): Builder
    {
        return Complaint::query()

            ->with([
                'consumer',
                'division',
                'category',
                'customerService',
                'verifier',
                'technicians',
                'maintenanceReport',
                'aiAnalysis',
            ])

            ->withCount('technicians')

            ->whereHas(
                'technicians',
                function (Builder $query) use ($technicianId) {

                    $query->where(
                        'users.id',
                        $technicianId
                    );
                }
            );
    }


    /**
     * Lightweight base query used for statistics.
     */
    private function assignedComplaintsQuery(
        int $technicianId
    ): Builder {

        return Complaint::query()

            ->whereHas(
                'technicians',
                function (Builder $query) use ($technicianId) {

                    $query->where(
                        'users.id',
                        $technicianId
                    );
                }
            );
    }
}
