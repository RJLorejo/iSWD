<?php

namespace App\Http\Controllers\CustomerService;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceWorkOrder;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class MaintenanceMonitoringController extends Controller
{
    public function pendingWorkloads(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'status' => ['nullable', 'in:all,awaiting,planned'],
        ]);

        $search = trim($filters['search'] ?? '');
        $status = $filters['status'] ?? 'awaiting';

        $baseQuery = MaintenanceWorkOrder::query()
            ->where('status', 'Pending Workload')
            ->whereNotNull('pending_recorded_at')
            ->whereNotNull('pending_reason')
            ->whereHas('complaint', function ($query) {
                $query->whereNotIn('status', [
                    'Completed',
                    'Closed',
                ]);
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

        $applyFollowUpFilter = function ($query, string $mode) {
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
        };

        $query = clone $baseQuery;

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('pending_reason', 'like', "%{$search}%")
                    ->orWhereHas('complaint', function ($complaint) use ($search) {
                        $complaint
                            ->where('complaint_no', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%")
                            ->orWhere('address', 'like', "%{$search}%");
                    });
            });
        }

        $applyFollowUpFilter($query, $status);

        $workOrders = $query
            ->with([
                'complaint.category',
                'complaint.division',
                'technicians',
                'schedule',
            ])
            ->orderByDesc('pending_recorded_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        // Load follow-up information for the displayed page.
        $followUps = collect();

        $complaintIds = $workOrders->getCollection()
            ->pluck('complaint_id')
            ->unique()
            ->values();

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

        $counts = [
            'all' => (clone $baseQuery)->count(),

            'awaiting' => tap(clone $baseQuery, function ($q) use ($applyFollowUpFilter) {
                $applyFollowUpFilter($q, 'awaiting');
            })->count(),

            'planned' => tap(clone $baseQuery, function ($q) use ($applyFollowUpFilter) {
                $applyFollowUpFilter($q, 'planned');
            })->count(),
        ];

        return view(
            'customer-service.maintenance-monitoring.pending-workloads',
            compact('workOrders', 'counts', 'search', 'status')
        );
    }

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

        $baseQuery = MaintenanceWorkOrder::query()
            ->where('status', 'Scheduled')
            ->whereNull('started_at')
            ->whereHas('schedule', function ($query) use ($today) {
                $query->whereDate('schedule_date', '<', $today)
                    ->where('status', 'Finalized');
            });

        $totalMissed = (clone $baseQuery)->count();

        $query = clone $baseQuery;

        if ($search !== '') {
            $query->whereHas('complaint', function ($complaint) use ($search) {
                $complaint
                    ->where('complaint_no', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if ($dateFrom !== '') {
            $query->whereHas('schedule', function ($q) use ($dateFrom) {
                $q->whereDate('schedule_date', '>=', $dateFrom);
            });
        }

        if ($dateTo !== '') {
            $query->whereHas('schedule', function ($q) use ($dateTo) {
                $q->whereDate('schedule_date', '<=', $dateTo);
            });
        }

        $workOrders = $query
            ->with([
                'complaint.category',
                'complaint.division',
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
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        return view(
            'customer-service.maintenance-monitoring.missed-schedules',
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
     * Print the filtered pending workloads report.
     */
    public function printPendingWorkloads(Request $request)
    {
        $data = $this->pendingWorkloads($request)->getData();

        return view(
            'customer-service.maintenance-monitoring.pending-workloads-print',
            $data
        );
    }

    /**
     * Print the filtered missed schedules report.
     */
    public function printMissedSchedules(Request $request)
    {
        $data = $this->missedSchedules($request)->getData();

        return view(
            'customer-service.maintenance-monitoring.missed-schedules-print',
            $data
        );
    }
}
