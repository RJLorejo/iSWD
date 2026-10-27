<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceWorkOrder;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class MaintenanceMonitoringController extends Controller
{
    /**
     * Pending workloads that have not been resolved by a completed
     * follow-up work order.
     */
    private function pendingWorkloadsQuery(): Builder
    {
        return MaintenanceWorkOrder::query()
            ->where('maintenance_work_orders.status', 'Pending Workload')
            ->whereNotNull('maintenance_work_orders.pending_recorded_at')
            ->whereNotNull('maintenance_work_orders.pending_reason')
            ->whereHas('complaint', function ($query) {
                $query->whereNotIn('status', ['Completed', 'Closed']);
            })
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('maintenance_work_orders as resolved_follow_up')
                    ->whereColumn(
                        'resolved_follow_up.complaint_id',
                        'maintenance_work_orders.complaint_id'
                    )
                    ->whereColumn(
                        'resolved_follow_up.id',
                        '>',
                        'maintenance_work_orders.id'
                    )
                    ->where('resolved_follow_up.status', 'Completed');
            });
    }

    /**
     * Filter pending workloads by whether a valid follow-up exists.
     */
    private function applyFollowUpFilter(Builder $query, string $mode): void
    {
        $followUpExists = function ($subquery) {
            $subquery->selectRaw('1')
                ->from('maintenance_work_orders as follow_up')
                ->whereColumn(
                    'follow_up.complaint_id',
                    'maintenance_work_orders.complaint_id'
                )
                ->whereColumn(
                    'follow_up.id',
                    '>',
                    'maintenance_work_orders.id'
                )
                ->whereNotIn('follow_up.status', [
                    'Cancelled',
                    'Rescheduled',
                ]);
        };

        if ($mode === 'planned') {
            $query->whereExists($followUpExists);
        } elseif ($mode === 'awaiting') {
            $query->whereNotExists($followUpExists);
        }
    }

    /**
     * Apply search and follow-up filters to pending workloads.
     */
    private function filterPendingWorkloads(
        Builder $query,
        Request $request,
        string $status
    ): Builder {
        $search = trim($request->input('search', ''));

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where(
                    'maintenance_work_orders.pending_reason',
                    'like',
                    "%{$search}%"
                )->orWhereHas('complaint', function ($complaint) use ($search) {
                    $complaint
                        ->where('complaint_no', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            });
        }

        $this->applyFollowUpFilter($query, $status);

        return $query;
    }

    /**
     * Display pending workloads.
     */
    public function pendingWorkloads(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'status' => ['nullable', 'in:all,awaiting,planned'],
        ]);

        $search = trim($filters['search'] ?? '');
        $status = $filters['status'] ?? 'awaiting';

        $baseQuery = $this->pendingWorkloadsQuery();

        $query = $this->filterPendingWorkloads(
            clone $baseQuery,
            $request,
            $status
        );

        $workOrders = $query
            ->with([
                'complaint.category',
                'complaint.division',
                'complaint.consumer',
                'technicians',
                'schedule',
            ])
            ->orderByDesc('pending_recorded_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $counts = [
            'all' => (clone $baseQuery)->count(),

            'awaiting' => tap(clone $baseQuery, function ($q) {
                $this->applyFollowUpFilter($q, 'awaiting');
            })->count(),

            'planned' => tap(clone $baseQuery, function ($q) {
                $this->applyFollowUpFilter($q, 'planned');
            })->count(),
        ];

        $complaintIds = $workOrders->getCollection()
            ->pluck('complaint_id')
            ->unique()
            ->values();

        $followUps = collect();

        if ($complaintIds->isNotEmpty()) {
            $followUps = MaintenanceWorkOrder::query()
                ->with('schedule')
                ->whereIn('complaint_id', $complaintIds)
                ->whereNotIn('status', ['Cancelled', 'Rescheduled'])
                ->orderByDesc('id')
                ->get()
                ->groupBy('complaint_id');
        }

        $workOrders->getCollection()->each(function ($order) use ($followUps) {
            $order->setRelation(
                'displayFollowUp',
                $followUps->get($order->complaint_id, collect())
                    ->first(fn($candidate) => $candidate->id > $order->id)
            );
        });

        return view(
            'admin.maintenance-monitoring.pending-workloads',
            compact('workOrders', 'counts', 'search', 'status')
        );
    }

    /**
     * Print all pending workloads matching the selected filters.
     */
    public function printPendingWorkloads(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'status' => ['nullable', 'in:all,awaiting,planned'],
        ]);

        $search = trim($filters['search'] ?? '');
        $status = $filters['status'] ?? 'awaiting';

        $baseQuery = $this->pendingWorkloadsQuery();

        $counts = [
            'all' => (clone $baseQuery)->count(),

            'awaiting' => tap(clone $baseQuery, function ($query) {
                $this->applyFollowUpFilter($query, 'awaiting');
            })->count(),

            'planned' => tap(clone $baseQuery, function ($query) {
                $this->applyFollowUpFilter($query, 'planned');
            })->count(),
        ];

        $workOrders = $this->filterPendingWorkloads(
            clone $baseQuery,
            $request,
            $status
        )
            ->with([
                'complaint.category',
                'complaint.division',
                'complaint.consumer',
                'technicians',
                'schedule',
            ])
            ->orderByDesc('pending_recorded_at')
            ->orderByDesc('id')
            ->get();

        $complaintIds = $workOrders->pluck('complaint_id')->unique()->values();

        $followUps = collect();

        if ($complaintIds->isNotEmpty()) {
            $followUps = MaintenanceWorkOrder::query()
                ->with('schedule')
                ->whereIn('complaint_id', $complaintIds)
                ->whereNotIn('status', ['Cancelled', 'Rescheduled'])
                ->orderByDesc('id')
                ->get()
                ->groupBy('complaint_id');
        }

        $workOrders->each(function ($order) use ($followUps) {
            $order->setRelation(
                'displayFollowUp',
                $followUps->get($order->complaint_id, collect())
                    ->first(fn($candidate) => $candidate->id > $order->id)
            );
        });

        return view(
            'admin.maintenance-monitoring.pending-workloads-print',
            compact('workOrders', 'search', 'status', 'counts')
        );
    }

    /**
     * Build the missed schedules query.
     */
    private function missedSchedulesQuery(string $today): Builder
    {
        return MaintenanceWorkOrder::query()
            ->where('maintenance_work_orders.status', 'Scheduled')
            ->whereNull('maintenance_work_orders.started_at')
            ->whereHas('schedule', function ($query) use ($today) {
                $query->whereDate('schedule_date', '<', $today)
                    ->where('status', 'Finalized');
            });
    }

    /**
     * Apply missed schedule search and date filters.
     */
    private function filterMissedSchedules(
        Builder $query,
        Request $request
    ): Builder {
        $search = trim($request->input('search', ''));
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        if ($search !== '') {
            $query->whereHas('complaint', function ($complaint) use ($search) {
                $complaint
                    ->where('complaint_no', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if ($dateFrom) {
            $query->whereHas('schedule', function ($q) use ($dateFrom) {
                $q->whereDate('schedule_date', '>=', $dateFrom);
            });
        }

        if ($dateTo) {
            $query->whereHas('schedule', function ($q) use ($dateTo) {
                $q->whereDate('schedule_date', '<=', $dateTo);
            });
        }

        return $query;
    }

    /**
     * Display missed schedules.
     */
    public function missedSchedules(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:date_from',
            ],
        ]);

        $search = trim($filters['search'] ?? '');
        $dateFrom = $filters['date_from'] ?? '';
        $dateTo = $filters['date_to'] ?? '';
        $today = CarbonImmutable::now('Asia/Manila')->toDateString();

        $baseQuery = $this->missedSchedulesQuery($today);

        $totalMissed = (clone $baseQuery)->count();

        $workOrders = $this->filterMissedSchedules(
            clone $baseQuery,
            $request
        )
            ->with([
                'complaint.category',
                'complaint.division',
                'complaint.consumer',
                'complaint.technicians',
                'technicians',
                'schedule',
            ])
            ->orderBy(
                MaintenanceSchedule::query()
                    ->select('schedule_date')
                    ->whereColumn(
                        'maintenance_schedules.id',
                        'maintenance_work_orders.maintenance_schedule_id'
                    )
                    ->limit(1),
                'asc'
            )
            ->orderBy('maintenance_work_orders.id')
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.maintenance-monitoring.missed-schedules',
            compact(
                'workOrders',
                'totalMissed',
                'search',
                'dateFrom',
                'dateTo',
                'today'
            )
        );
    }

    /**
     * Print all missed schedules matching the selected filters.
     */
    public function printMissedSchedules(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:date_from',
            ],
        ]);

        $search = trim($filters['search'] ?? '');
        $dateFrom = $filters['date_from'] ?? '';
        $dateTo = $filters['date_to'] ?? '';
        $today = CarbonImmutable::now('Asia/Manila')->toDateString();

        $baseQuery = $this->missedSchedulesQuery($today);

        $totalMissed = (clone $baseQuery)->count();

        $workOrders = $this->filterMissedSchedules(
            clone $baseQuery,
            $request
        )
            ->with([
                'complaint.category',
                'complaint.division',
                'complaint.consumer',
                'complaint.technicians',
                'technicians',
                'schedule',
            ])
            ->orderBy(
                MaintenanceSchedule::query()
                    ->select('schedule_date')
                    ->whereColumn(
                        'maintenance_schedules.id',
                        'maintenance_work_orders.maintenance_schedule_id'
                    )
                    ->limit(1),
                'asc'
            )
            ->orderBy('maintenance_work_orders.id')
            ->get();

        return view(
            'admin.maintenance-monitoring.missed-schedules-print',
            compact(
                'workOrders',
                'totalMissed',
                'search',
                'dateFrom',
                'dateTo',
                'today'
            )
        );
    }
}
