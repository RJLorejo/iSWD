<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\MaintenanceWorkOrder;
use App\Models\MaintenanceScheduleHistory;
use App\Models\User;
use App\Services\TechnicianWorkOrderAccessService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    public function index(
        Request $request,
        TechnicianWorkOrderAccessService $workOrderAccess
    ) {
        $technicianId = (int) Auth::id();

        // Preserve All Complaints page while honoring dashboard filter links.
        $scope = 'all';
        $viewScope = $this->dashboardScope($request);

        // Dashboard visit filters use one row per work order, not per complaint.
        // This ensures visit counts agree with the dashboard cards.
        if (in_array($viewScope, [
            'scheduled',
            'in_progress',
            'pending_workload',
            'completed',
            'active',
            'today',
            'overdue',
        ], true)) {
            return $this->filteredWorkOrders($request, $technicianId, $viewScope);
        }

        $query = $this->complaintsQuery($technicianId);

        $this->applyFilters($query, $request, $scope, $technicianId);

        // Newest complaint first.
        $complaints = $query
            ->orderByDesc('complaints.created_at')
            ->orderByDesc('complaints.id')
            ->paginate(10)
            ->withQueryString();

        $this->attachScheduleInformation(
            $complaints,
            $workOrderAccess,
            $technicianId
        );

        $stats = $this->getComplaintStatistics($technicianId);

        return view('technician.complaints.index', array_merge(
            [
                'complaints' => $complaints,
                'scope' => $scope,
                'viewScope' => $viewScope,
                'pageTitle' => 'All Complaints',
                'pageDescription' => 'View all maintenance complaints assigned to you.',
                'sortDescription' => 'Newest complaints first',
            ],
            $stats
        ));
    }

    /**
     * Dashboard visit filters: one record per technician-assigned work order.
     * Keep the unfiltered All Complaints and FCFS Active Work Orders pages intact.
     */
    private function filteredWorkOrders(
        Request $request,
        int $technicianId,
        string $viewScope
    ) {
        $today = now('Asia/Manila')->toDateString();

        $query = MaintenanceWorkOrder::query()
            ->whereHas('technicians', function (Builder $team) use ($technicianId) {
                $team->where('users.id', $technicianId);
            })
            ->with([
                'complaint.consumer',
                'complaint.category',
                'complaint.aiAnalysis',
                'schedule',
            ]);

        $statuses = match ($viewScope) {
            'scheduled', 'today', 'overdue' => ['Scheduled'],
            'in_progress' => ['In Progress'],
            'pending_workload' => ['Pending Workload'],
            'completed' => ['Completed'],
            'active' => ['Scheduled', 'In Progress'],
        };

        $query->whereIn('maintenance_work_orders.status', $statuses);

        // Dashboard execution metrics include only approved visits.
        $query->where('maintenance_work_orders.approval_status', 'Approved')
            ->whereHas('schedule', fn(Builder $schedule) => $schedule->where('status', 'Finalized'));

        if ($viewScope !== 'completed') {
            $query->whereHas('complaint', function (Builder $complaint) {
                $complaint->whereNotIn('complaints.status', [
                    'Completed',
                    'Closed',
                    'Rejected',
                ]);
            });
        }

        if ($viewScope === 'pending_workload') {
            $query->whereNotExists(function ($subquery) {
                $subquery->selectRaw('1')
                    ->from('maintenance_work_orders as later_visits')
                    ->whereColumn('later_visits.complaint_id', 'maintenance_work_orders.complaint_id')
                    ->whereColumn('later_visits.visit_number', '>', 'maintenance_work_orders.visit_number')
                    ->where('later_visits.status', 'Completed');
            });
        }

        if (in_array($viewScope, ['today', 'overdue'], true)) {
            $query->whereHas('schedule', function (Builder $schedule) use ($viewScope, $today) {
                $schedule->where('status', 'Finalized')
                    ->whereDate('schedule_date', $viewScope === 'today' ? '=' : '<', $today);
            });
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->query('search'));
            $query->whereHas('complaint', function (Builder $complaint) use ($search) {
                $complaint->where(function (Builder $q) use ($search) {
                    $q->where('complaint_no', 'like', "%{$search}%")
                        ->orWhere('complainant_name', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%")
                        ->orWhereHas('category', function (Builder $category) use ($search) {
                            $category->where('name', 'like', "%{$search}%");
                        });
                });
            });
        }

        if ($request->filled('urgency')) {
            $urgency = strtoupper(trim((string) $request->query('urgency')));

            if (in_array($urgency, ['HIGH', 'MODERATE', 'LOW'], true)) {
                $query->whereHas('complaint.aiAnalysis', function (Builder $ai) use ($urgency) {
                    $ai->whereRaw(
                        "UPPER(TRIM(verified_urgency_level)) = ?",
                        [$urgency]
                    );
                });
            } elseif ($urgency === 'NOT_ASSESSED') {
                $query->where(function (Builder $query) {
                    $query->whereDoesntHave('complaint.aiAnalysis')
                        ->orWhereHas('complaint.aiAnalysis', function (Builder $ai) {
                            $ai->whereNull('verified_urgency_level')
                                ->orWhereRaw("TRIM(verified_urgency_level) = ''");
                        });
                });
            }
        }

        if ($request->filled('date_from')) {
            $query->whereHas('schedule', function (Builder $schedule) use ($request) {
                $schedule->whereDate('schedule_date', '>=', $request->query('date_from'));
            });
        }
        if ($request->filled('date_to')) {
            $query->whereHas('schedule', function (Builder $schedule) use ($request) {
                $schedule->whereDate('schedule_date', '<=', $request->query('date_to'));
            });
        }

        $orders = $query
            ->orderByRaw("CASE maintenance_work_orders.status WHEN 'In Progress' THEN 1 WHEN 'Scheduled' THEN 2 ELSE 3 END")
            ->orderByDesc('maintenance_work_orders.id')
            ->paginate(10)
            ->withQueryString();

        $labels = [
            'scheduled' => 'Scheduled Visits',
            'in_progress' => 'In Progress Visits',
            'pending_workload' => 'Unresolved Pending Visits',
            'completed' => 'Accomplished Visits',
            'active' => 'Active Visits',
            'today' => "Today's Scheduled Visits",
            'overdue' => 'Overdue Scheduled Visits',
        ];

        return view('technician.complaints.work-order-results', [
            'orders' => $orders,
            'viewScope' => $viewScope,
            'pageTitle' => $labels[$viewScope],
        ]);
    }

    /**
     * Active Work Orders
     *
     * First Come, First Served based on the technician's
     * recorded assignment date and time.
     */
    public function activeWorkOrders(
        Request $request,
        TechnicianWorkOrderAccessService $workOrderAccess
    ) {
        $technicianId = (int) Auth::id();

        $scope = 'active';

        $query = $this->complaintsQuery($technicianId)
            ->whereHas('maintenanceWorkOrders', function (Builder $orders) use ($technicianId) {

                $orders->whereNotIn('maintenance_work_orders.status', [
                    'Cancelled',
                    'Rescheduled',
                ]);

                $orders->whereNotExists(function ($subquery) {
                    $subquery->selectRaw('1')
                        ->from('maintenance_work_orders as newer_orders')
                        ->whereColumn(
                            'newer_orders.complaint_id',
                            'maintenance_work_orders.complaint_id'
                        )
                        ->whereNotIn('newer_orders.status', [
                            'Cancelled',
                            'Rescheduled',
                        ])
                        ->where(function ($query) {
                            $query->whereColumn(
                                'newer_orders.visit_number',
                                '>',
                                'maintenance_work_orders.visit_number'
                            )->orWhere(function ($query) {
                                $query->whereColumn(
                                    'newer_orders.visit_number',
                                    '=',
                                    'maintenance_work_orders.visit_number'
                                )->whereColumn(
                                    'newer_orders.id',
                                    '>',
                                    'maintenance_work_orders.id'
                                );
                            });
                        });
                });

                $orders->whereHas('technicians', function (Builder $team) use ($technicianId) {
                    $team->where('users.id', $technicianId);
                });

                $orders->whereIn('maintenance_work_orders.status', [
                    'Scheduled',
                    'In Progress',
                ]);
            });

        $this->applyFilters($query, $request, $scope, $technicianId);

        // Oldest technician assignment first.
        $assignmentDate = \Illuminate\Support\Facades\DB::table(
            'complaint_technicians'
        )
            ->selectRaw('MIN(assigned_at)')
            ->whereColumn(
                'complaint_technicians.complaint_id',
                'complaints.id'
            )
            ->where(
                'complaint_technicians.technician_id',
                $technicianId
            );

        $complaints = $query
            ->orderByRaw(
                '(' . $assignmentDate->toSql() . ') IS NULL ASC',
                $assignmentDate->getBindings()
            )
            ->orderBy($assignmentDate, 'asc')
            ->orderBy('complaints.id', 'asc')
            ->paginate(10)
            ->withQueryString();

        $this->attachScheduleInformation(
            $complaints,
            $workOrderAccess,
            $technicianId
        );

        $stats = $this->getComplaintStatistics($technicianId);

        return view('technician.complaints.index', array_merge(
            [
                'complaints' => $complaints,
                'scope' => $scope,
                'viewScope' => 'active',
                'pageTitle' => 'Active Work Orders',
                'pageDescription' => 'Your active maintenance assignments, ordered by assignment time.',
                'sortDescription' => 'Oldest assignments first',
            ],
            $stats
        ));
    }

    /**
     * Attach the current technician's work-order schedule
     * to the displayed complaints.
     */
    private function attachScheduleInformation(
        $complaints,
        TechnicianWorkOrderAccessService $workOrderAccess,
        int $technicianId
    ): void {
        $complaints->getCollection()->each(
            function (Complaint $complaint) use (
                $workOrderAccess,
                $technicianId
            ) {
                $workOrder = $workOrderAccess->findForTechnician(
                    $complaint,
                    $technicianId
                );

                $complaint->setRelation(
                    'myScheduledWorkOrder',
                    $workOrder
                );

                $complaint->myScheduleState = $workOrderAccess
                    ->status($workOrder, $complaint);
            }
        );
    }

    /**
     * Shared statistics for both complaint pages.
     */
    private function getComplaintStatistics(int $technicianId): array
    {
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
            ->whereIn('status', ['Assigned', 'In Progress'])
            ->whereHas('aiAnalysis', function (Builder $query) {
                $query->whereRaw(
                    "UPPER(TRIM(verified_urgency_level)) = ?",
                    ['HIGH']
                );
            })
            ->count();


        return [
            'assignedCount' => $assignedCount,
            'inProgressCount' => $inProgressCount,
            'urgentCount' => $urgentCount,
            'activeCount' => $assignedCount + $inProgressCount,
        ];
    }

    public function printReport(Request $request)
    {
        $technicianId = (int) Auth::id();

        $scope = $request->get('scope', 'active');

        if (!in_array($scope, ['active', 'all', 'scheduled', 'in_progress', 'pending_workload', 'completed', 'high_urgency', 'today', 'overdue'], true)) {
            $scope = 'active';
        }

        $query = $this->complaintsQuery($technicianId);

        $this->applyFilters($query, $request, $scope, $technicianId);

        $complaints = $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        return view(
            'technician.complaints.print-report',
            compact('complaints', 'scope')
        );
    }


    public function show(
        Complaint $complaint,
        TechnicianWorkOrderAccessService $workOrderAccess
    ) {
        $technicianId = (int) Auth::id();

        $isAssignedToMe = $complaint
            ->technicians()
            ->where('users.id', $technicianId)
            ->exists();

        $workOrder = $workOrderAccess->findForTechnician(
            $complaint,
            $technicianId
        );

        abort_unless($isAssignedToMe || $workOrder, 403);

        $complaint->load([
            'consumer.address',
            'division',
            'category',
            'photos',
            'customerService',
            'verifier',
            'technicians',
            'maintenanceReport',
            'aiAnalysis',
            'commercialResolution.processor',
            'commercialResolution.forwarder',
        ]);

        $scheduleState = $workOrderAccess->status(
            $workOrder,
            $complaint
        );

        $workOrderTeam = $workOrder
            ? $workOrder->technicians()->get()
            : collect();

        $isCurrentWorkOrderTechnician = $workOrder
            ? $workOrderTeam->contains(
                fn($technician) => (int) $technician->id === $technicianId
            )
            : false;

        $currentVisit = MaintenanceWorkOrder::query()
            ->with(['schedule', 'technicians'])
            ->where('complaint_id', $complaint->id)
            ->whereNotIn('status', [
                'Cancelled',
                'Rescheduled',
            ])
            ->orderByDesc('visit_number')
            ->orderByDesc('id')
            ->first();

        $currentMaintenanceTeam = $currentVisit
            ? $currentVisit->technicians
            : collect();

        // A past assignee can view history, but the current visit belongs
        // exclusively to the latest work order's dedicated team.
        $isReassignedFromCurrentVisit = $currentVisit !== null
            && !$currentMaintenanceTeam->contains(
                fn($technician) => (int) $technician->id === $technicianId
            )
            && $complaint->technicians->contains(
                fn($technician) => (int) $technician->id === $technicianId
            );

        $previousMaintenanceTeam = $complaint->technicians
            ->filter(function ($technician) use ($currentMaintenanceTeam) {
                return !$currentMaintenanceTeam->contains(
                    'id',
                    $technician->id
                );
            })
            ->values();



        $today = now('Asia/Manila')->toDateString();

        $maintenanceStartedAt = $workOrder
            ? $workOrder->started_at
            : (
                $complaint->maintenanceReport?->started_at
                ?? $complaint->technicians
                ->pluck('pivot.started_at')
                ->filter()
                ->sort()
                ->first()
            );

        $startedDate = $maintenanceStartedAt
            ? \Carbon\Carbon::parse($maintenanceStartedAt)
            ->timezone('Asia/Manila')
            ->toDateString()
            : null;

        $isPreviousDayUnfinished = (
            $complaint->status === 'In Progress'
            && $startedDate !== null
            && $startedDate < $today
            && (!$workOrder || $workOrder->status === 'In Progress')
        );

        $isPendingWorkload = (
            $workOrder?->status === 'Pending Workload'
        );

        $requiresLegacyReconciliation = (
            $isPreviousDayUnfinished
            && !$workOrder
        );

        $canContinueAccomplishment = (
            $isCurrentWorkOrderTechnician
            && $complaint->status === 'In Progress'
            && !$isPreviousDayUnfinished
            && !$isPendingWorkload
            && $workOrder?->status === 'In Progress'
            && $workOrder->schedule?->status === 'Finalized'
            && $workOrder->approval_status === 'Approved'
            && $workOrder->schedule?->schedule_date !== null
            && \Carbon\Carbon::parse(
                $workOrder->schedule->schedule_date,
                'Asia/Manila'
            )->toDateString() === $today
        );

        // Plumber replacement history for this complaint.
        $plumberReplacementHistories = MaintenanceScheduleHistory::query()
            ->with('performer')
            ->where('action', 'Plumber Replaced')
            ->whereHas('workOrder', function ($query) use ($complaint) {
                $query->where('complaint_id', $complaint->id);
            })
            ->orderBy('created_at')
            ->get();

        // Collect the previous and replacement plumber IDs from the history.
        $plumberIds = $plumberReplacementHistories
            ->flatMap(function ($history) {
                $oldValues = $history->old_values ?? [];
                $newValues = $history->new_values ?? [];

                return [
                    $oldValues['replaced_technician_id'] ?? null,
                    $newValues['replacement_technician_id'] ?? null,
                ];
            })
            ->filter()
            ->unique()
            ->values();

        // Retrieve plumber names in one query.
        $plumberNames = User::query()
            ->whereIn('id', $plumberIds)
            ->get()
            ->mapWithKeys(function ($user) {
                return [
                    $user->id => $user->full_name
                        ?: $user->name
                        ?: 'Name unavailable',
                ];
            });

        // Attach readable names to each history record.
        $plumberReplacementHistories->each(function ($history) use ($plumberNames) {
            $oldValues = $history->old_values ?? [];
            $newValues = $history->new_values ?? [];

            $previousPlumberId = $oldValues['replaced_technician_id'] ?? null;
            $replacementPlumberId = $newValues['replacement_technician_id'] ?? null;

            $history->previous_plumber_name = $plumberNames->get(
                $previousPlumberId,
                'Name unavailable'
            );

            $history->replacement_plumber_name = $plumberNames->get(
                $replacementPlumberId,
                'Name unavailable'
            );
        });

        // Group history by work order for the Blade view.
        $plumberReplacementHistories = $plumberReplacementHistories
            ->groupBy('maintenance_work_order_id');

        return view('technician.complaints.show', compact(
            'complaint',
            'workOrder',
            'scheduleState',
            'workOrderTeam',
            'isPreviousDayUnfinished',
            'isPendingWorkload',
            'requiresLegacyReconciliation',
            'canContinueAccomplishment',
            'maintenanceStartedAt',
            'currentVisit',
            'currentMaintenanceTeam',
            'isReassignedFromCurrentVisit',
            'previousMaintenanceTeam',
            'plumberReplacementHistories'
        ));
    }


    private function applyFilters(
        Builder $query,
        Request $request,
        string $scope,
        int $technicianId
    ): void {
        if ($scope === 'active' && $request->route()?->getName() === 'technician.active-work-orders.index') {
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

        $dashboardScope = $this->dashboardScope($request);
        if ($scope !== 'active' || $request->route()?->getName() !== 'technician.active-work-orders.index') {
            if ($scope !== 'all' && $scope !== 'active') {
                $dashboardScope = $scope; // Print route receives the scope directly.
            }
        }


        if ($dashboardScope === 'high_urgency') {
            $query->whereIn('complaints.status', ['Assigned', 'In Progress'])
                ->whereHas('aiAnalysis', function (Builder $ai) {
                    $ai->whereRaw(
                        "UPPER(TRIM(verified_urgency_level)) = ?",
                        ['HIGH']
                    );
                });
        }


        $statuses = match ($dashboardScope) {
            'scheduled', 'today', 'overdue' => ['Scheduled'],
            'in_progress' => ['In Progress'],
            'pending_workload' => ['Pending Workload'],
            'completed' => ['Completed'],
            'active' => ['Scheduled', 'In Progress'],
            default => [],
        };

        if ($statuses !== []) {
            $today = now('Asia/Manila')->toDateString();
            $query->whereHas('maintenanceWorkOrders', function (Builder $orders) use ($technicianId, $statuses, $dashboardScope, $today) {
                $orders->whereIn('maintenance_work_orders.status', $statuses)
                    ->where('maintenance_work_orders.approval_status', 'Approved')
                    ->whereHas('schedule', fn(Builder $schedule) => $schedule->where('status', 'Finalized'))
                    ->whereHas('technicians', function (Builder $team) use ($technicianId) {
                        $team->where('users.id', $technicianId);
                    });

                if (in_array($dashboardScope, ['scheduled', 'in_progress', 'pending_workload', 'active', 'today', 'overdue'], true)) {
                    $orders->whereHas('complaint', function (Builder $complaint) {
                        $complaint->whereNotIn('complaints.status', ['Completed', 'Closed', 'Rejected']);
                    });
                }

                if ($dashboardScope === 'pending_workload') {
                    $orders->whereNotExists(function ($subquery) {
                        $subquery->selectRaw('1')
                            ->from('maintenance_work_orders as later_visits')
                            ->whereColumn('later_visits.complaint_id', 'maintenance_work_orders.complaint_id')
                            ->whereColumn('later_visits.visit_number', '>', 'maintenance_work_orders.visit_number')
                            ->where('later_visits.status', 'Completed');
                    });
                }

                if ($dashboardScope === 'today' || $dashboardScope === 'overdue') {
                    $orders->whereHas('schedule', function (Builder $schedule) use ($dashboardScope, $today) {
                        $schedule->whereDate('schedule_date', $dashboardScope === 'today' ? '=' : '<', $today);
                    });
                }
            });
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);

            $query->where(function (Builder $query) use ($search) {
                $query
                    ->where('complaint_no', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('landmark', 'like', "%{$search}%")
                    ->orWhere('complainant_name', 'like', "%{$search}%")
                    ->orWhere('complainant_phone', 'like', "%{$search}%")
                    ->orWhereHas('consumer', function (Builder $consumer) use ($search) {
                        $consumer
                            ->where('first_name', 'like', "%{$search}%")
                            ->orWhere('middle_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('account_number', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('category', function (Builder $category) use ($search) {
                        $category
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $allowed = [
                'Assigned',
                'In Progress',
                'Completed',
                'Closed',
            ];

            if (in_array($request->status, $allowed, true)) {
                $query->where('status', $request->status);
            }
        }


        if ($request->filled('urgency')) {
            $urgency = strtoupper(trim((string) $request->query('urgency')));

            if (in_array($urgency, ['HIGH', 'MODERATE', 'LOW'], true)) {
                $query->whereHas('aiAnalysis', function (Builder $aiQuery) use ($urgency) {
                    $aiQuery->whereRaw(
                        "UPPER(TRIM(verified_urgency_level)) = ?",
                        [$urgency]
                    );
                });
            } elseif ($urgency === 'NOT_ASSESSED') {
                $query->where(function (Builder $urgencyQuery) {
                    $urgencyQuery
                        ->whereDoesntHave('aiAnalysis')
                        ->orWhereHas('aiAnalysis', function (Builder $aiQuery) {
                            $aiQuery->whereNull('verified_urgency_level')
                                ->orWhereRaw("TRIM(verified_urgency_level) = ''");
                        });
                });
            }
        }


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
    }

    private function dashboardScope(Request $request): string
    {
        $allowed = [
            'all',
            'active',
            'scheduled',
            'in_progress',
            'pending_workload',
            'completed',
            'high_urgency',
            'today',
            'overdue',
        ];

        $scope = (string) $request->query('scope', 'all');
        return in_array($scope, $allowed, true) ? $scope : 'all';
    }

    private function complaintsQuery(int $technicianId): Builder
    {
        return Complaint::query()
            ->with([
                'consumer.address',
                'division',
                'category',
                'customerService',
                'verifier',
                'technicians',
                'maintenanceReport',
                'aiAnalysis',
                'commercialResolution.processor',
                'commercialResolution.forwarder',
            ])
            ->withCount('technicians')
            ->whereHas(
                'technicians',
                function (Builder $query) use ($technicianId) {
                    $query->where('users.id', $technicianId);
                }
            );
    }

    private function assignedComplaintsQuery(
        int $technicianId
    ): Builder {
        return Complaint::query()
            ->whereHas(
                'technicians',
                function (Builder $query) use ($technicianId) {
                    $query->where('users.id', $technicianId);
                }
            );
    }
}
