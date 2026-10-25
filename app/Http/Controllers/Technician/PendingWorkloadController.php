<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceWorkOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PendingWorkloadController extends Controller
{
    public function index(Request $request)
    {
        $technicianId = (int) Auth::id();

        $request->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'filter' => [
                'nullable',
                'in:all,awaiting_followup,followup_scheduled',
            ],
        ]);

        $filter = $request->query('filter', 'all');

        /*
        |--------------------------------------------------------------------------
        | BASE QUERY
        |--------------------------------------------------------------------------
        |
        | Only pending work orders assigned to the current technician.
        |
        | A later completed visit resolves the earlier pending visit.
        | We compare visit_number within the same complaint.
        |
        */

        $baseQuery = function () use ($technicianId): Builder {
            return MaintenanceWorkOrder::query()
                ->whereHas('technicians', function (Builder $query) use ($technicianId) {
                    $query->where('users.id', $technicianId);
                })
                ->where(
                    'maintenance_work_orders.status',
                    'Pending Workload'
                )
                ->whereNotExists(function ($query) {
                    $query
                        ->selectRaw('1')
                        ->from('maintenance_work_orders as completed_followups')
                        ->whereColumn(
                            'completed_followups.complaint_id',
                            'maintenance_work_orders.complaint_id'
                        )
                        ->whereColumn(
                            'completed_followups.visit_number',
                            '>',
                            'maintenance_work_orders.visit_number'
                        )
                        ->where(
                            'completed_followups.status',
                            'Completed'
                        );
                });
        };

        /*
        |--------------------------------------------------------------------------
        | FOLLOW-UP EXISTS
        |--------------------------------------------------------------------------
        */

        $hasFollowup = function (Builder $query): Builder {
            return $query->whereExists(function ($subquery) {
                $subquery
                    ->selectRaw('1')
                    ->from('maintenance_work_orders as followups')
                    ->whereColumn(
                        'followups.complaint_id',
                        'maintenance_work_orders.complaint_id'
                    )
                    ->whereColumn(
                        'followups.visit_number',
                        '>',
                        'maintenance_work_orders.visit_number'
                    );
            });
        };

        /*
        |--------------------------------------------------------------------------
        | STATISTICS
        |--------------------------------------------------------------------------
        */

        $totalPending = $baseQuery()->count();

        $awaitingFollowupCount = $baseQuery()
            ->whereNotExists(function ($query) {
                $query
                    ->selectRaw('1')
                    ->from('maintenance_work_orders as followups')
                    ->whereColumn(
                        'followups.complaint_id',
                        'maintenance_work_orders.complaint_id'
                    )
                    ->whereColumn(
                        'followups.visit_number',
                        '>',
                        'maintenance_work_orders.visit_number'
                    );
            })
            ->count();

        $followupScheduledCount = $hasFollowup($baseQuery())
            ->count();

        /*
        |--------------------------------------------------------------------------
        | OLDEST PENDING RECORD
        |--------------------------------------------------------------------------
        */

        $oldestPending = $baseQuery()
            ->whereNotNull('pending_recorded_at')
            ->orderBy('pending_recorded_at')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | PENDING WORKLOAD LIST
        |--------------------------------------------------------------------------
        */

        $query = $baseQuery()->with([
            'schedule',
            'complaint.consumer',
            'complaint.category',
            'complaint.aiAnalysis',
            'technicians',
            'histories',
        ]);

        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        if ($filter === 'awaiting_followup') {
            $query->whereNotExists(function ($subquery) {
                $subquery
                    ->selectRaw('1')
                    ->from('maintenance_work_orders as followups')
                    ->whereColumn(
                        'followups.complaint_id',
                        'maintenance_work_orders.complaint_id'
                    )
                    ->whereColumn(
                        'followups.visit_number',
                        '>',
                        'maintenance_work_orders.visit_number'
                    );
            });
        }

        if ($filter === 'followup_scheduled') {
            $hasFollowup($query);
        }

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {
            $search = trim((string) $request->search);

            $query->whereHas('complaint', function (Builder $complaintQuery) use ($search) {
                $complaintQuery
                    ->where('complaint_no', 'like', "%{$search}%")
                    ->orWhere('complainant_name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhereHas('category', function (Builder $categoryQuery) use ($search) {
                        $categoryQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        /*
        |--------------------------------------------------------------------------
        | SORTING
        |--------------------------------------------------------------------------
        */

        $workOrders = $query
            ->orderByRaw(
                'maintenance_work_orders.pending_recorded_at IS NULL ASC'
            )
            ->orderBy(
                'maintenance_work_orders.pending_recorded_at'
            )
            ->orderBy(
                'maintenance_work_orders.id'
            )
            ->paginate(10)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | LOAD LATER FOLLOW-UP VISITS
        |--------------------------------------------------------------------------
        |
        | Load only follow-ups associated with complaints on the current page.
        | Avoid querying the database once for every displayed work order.
        |
        */

        $complaintIds = $workOrders
            ->getCollection()
            ->pluck('complaint_id')
            ->unique()
            ->values();

        $followupsByComplaint = $complaintIds->isEmpty()
            ? collect()
            : MaintenanceWorkOrder::query()
                ->with('schedule')
                ->whereIn('complaint_id', $complaintIds)
                ->orderByDesc('visit_number')
                ->orderByDesc('id')
                ->get()
                ->groupBy('complaint_id');

        /*
        |--------------------------------------------------------------------------
        | ATTACH FOLLOW-UP DISPLAY INFORMATION
        |--------------------------------------------------------------------------
        */

        $workOrders->getCollection()->each(function ($workOrder) use ($followupsByComplaint) {

            $followup = $followupsByComplaint
                ->get($workOrder->complaint_id, collect())
                ->first(function ($candidate) use ($workOrder) {
                    return $candidate->visit_number > $workOrder->visit_number;
                });

            $workOrder->setRelation('latestFollowup', $followup);

            if (!$followup) {
                $workOrder->followup_display_status = 'Awaiting Reschedule';
                return;
            }

            if ($followup->status === 'In Progress') {
                $workOrder->followup_display_status = 'Follow-up In Progress';
                return;
            }

            if ($followup->schedule?->status !== 'Finalized') {
                $workOrder->followup_display_status = 'Awaiting Manager Finalization';
                return;
            }

            $workOrder->followup_display_status = 'Follow-up Scheduled';
        });

        return view('technician.pending-workload.index', compact(
            'workOrders',
            'totalPending',
            'awaitingFollowupCount',
            'followupScheduledCount',
            'oldestPending',
            'filter'
        ));
    }

    /**
     * Print every unresolved pending visit matching the current filters.
     * Access is limited to work orders assigned to the signed-in technician.
     */
    public function print(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'filter' => ['nullable', 'in:all,awaiting_followup,followup_scheduled'],
        ]);

        $technicianId = (int) Auth::id();
        $filter = $validated['filter'] ?? 'all';

        $query = MaintenanceWorkOrder::query()
            ->whereHas('technicians', function (Builder $team) use ($technicianId) {
                $team->where('users.id', $technicianId);
            })
            ->where('maintenance_work_orders.status', 'Pending Workload')
            ->whereNotExists(function ($subquery) {
                $subquery->selectRaw('1')
                    ->from('maintenance_work_orders as completed_followups')
                    ->whereColumn('completed_followups.complaint_id', 'maintenance_work_orders.complaint_id')
                    ->whereColumn('completed_followups.visit_number', '>', 'maintenance_work_orders.visit_number')
                    ->where('completed_followups.status', 'Completed');
            })
            ->with(['schedule', 'complaint.consumer', 'complaint.category', 'complaint.aiAnalysis']);

        if ($filter === 'awaiting_followup') {
            $query->whereNotExists(function ($subquery) {
                $subquery->selectRaw('1')
                    ->from('maintenance_work_orders as followups')
                    ->whereColumn('followups.complaint_id', 'maintenance_work_orders.complaint_id')
                    ->whereColumn('followups.visit_number', '>', 'maintenance_work_orders.visit_number');
            });
        } elseif ($filter === 'followup_scheduled') {
            $query->whereExists(function ($subquery) {
                $subquery->selectRaw('1')
                    ->from('maintenance_work_orders as followups')
                    ->whereColumn('followups.complaint_id', 'maintenance_work_orders.complaint_id')
                    ->whereColumn('followups.visit_number', '>', 'maintenance_work_orders.visit_number');
            });
        }

        if (!empty($validated['search'])) {
            $search = trim($validated['search']);
            $query->whereHas('complaint', function (Builder $complaintQuery) use ($search) {
                $complaintQuery->where(function (Builder $match) use ($search) {
                    $match->where('complaint_no', 'like', "%{$search}%")
                        ->orWhere('complainant_name', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%")
                        ->orWhereHas('category', function (Builder $category) use ($search) {
                            $category->where('name', 'like', "%{$search}%");
                        });
                });
            });
        }

        $workOrders = $query
            ->orderByRaw('maintenance_work_orders.pending_recorded_at IS NULL ASC')
            ->orderBy('maintenance_work_orders.pending_recorded_at')
            ->orderBy('maintenance_work_orders.id')
            ->get();

        $complaintIds = $workOrders->pluck('complaint_id')->unique()->values();
        $followupsByComplaint = $complaintIds->isEmpty()
            ? collect()
            : MaintenanceWorkOrder::query()
                ->with('schedule')
                ->whereIn('complaint_id', $complaintIds)
                ->orderByDesc('visit_number')
                ->orderByDesc('id')
                ->get()
                ->groupBy('complaint_id');

        $workOrders->each(function ($workOrder) use ($followupsByComplaint) {
            $followup = $followupsByComplaint
                ->get($workOrder->complaint_id, collect())
                ->first(function ($candidate) use ($workOrder) {
                    return $candidate->visit_number > $workOrder->visit_number;
                });

            $workOrder->setRelation('latestFollowup', $followup);

            if (!$followup) {
                $workOrder->followup_display_status = 'Awaiting Reschedule';
            } elseif ($followup->status === 'In Progress') {
                $workOrder->followup_display_status = 'Follow-up In Progress';
            } elseif ($followup->schedule?->status !== 'Finalized') {
                $workOrder->followup_display_status = 'Awaiting Manager Finalization';
            } else {
                $workOrder->followup_display_status = 'Follow-up Scheduled';
            }
        });

        return view('technician.pending-workload.print', compact('workOrders', 'filter'));
    }

}
