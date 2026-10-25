<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceWorkOrder;
use Illuminate\Http\Request;

class PendingWorkloadController extends Controller
{
    public function index(Request $request)
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

    // Only complaints with ongoing maintenance.
    ->whereHas('complaint', function ($query) {
        $query->whereNotIn('status', [
            'Completed',
            'Closed',
        ]);
    })

    // Exclude historical unfinished visits that have already
    // been resolved by a later completed follow-up work order.
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
            ->where(
                'resolved_follow_up.status',
                'Completed'
            );
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

        // Follow-up information for the visible page only.
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
                    ->first(fn ($candidate) => $candidate->id > $order->id)
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

        return view('maintenance-manager.pending-workloads.index', compact(
            'workOrders',
            'counts',
            'search',
            'status'
        ));
    }
}
