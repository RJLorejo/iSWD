<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceWorkOrder;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class BacklogController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $search = trim($filters['search'] ?? '');
        $dateFrom = $filters['date_from'] ?? '';
        $dateTo = $filters['date_to'] ?? '';

        $today = CarbonImmutable::now('Asia/Manila')
            ->toDateString();

        // A missed schedule is a Scheduled work order
        // whose planned date is already in the past.
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
            $query->where(function ($q) use ($search) {
                $q->whereHas('complaint', function ($complaint) use ($search) {
                    $complaint
                        ->where('complaint_no', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
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
                \App\Models\MaintenanceSchedule::query()
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

        return view('maintenance-manager.backlogs.index', compact(
            'workOrders',
            'totalMissed',
            'search',
            'dateFrom',
            'dateTo',
            'today'
        ));
    }
}
