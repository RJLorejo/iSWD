<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceWorkOrder;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaintenanceInsightsController extends Controller
{
    private function dates(Request $request): array
    {
        $validated = $request->validate([
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:date_from'],
            'status' => ['nullable', 'in:all,Scheduled,In Progress,Completed,Pending Workload,Rescheduled,Cancelled'],
        ]);
        $from = CarbonImmutable::parse($validated['date_from'] ?? now()->startOfYear()->toDateString())->startOfDay();
        $to = CarbonImmutable::parse($validated['date_to'] ?? now()->toDateString())->endOfDay();
        if ($to->lessThan($from)) {
            abort(422, 'Date To must not be earlier than Date From.');
        }
        return [$from, $to, $validated['status'] ?? 'all'];
    }

    private function baseQuery(CarbonImmutable $from, CarbonImmutable $to, string $status)
    {
        // The reporting period is the scheduled VISIT date, not the complaint creation date.
        // Only approved work orders on finalized schedules are included.
        $query = MaintenanceWorkOrder::query()
            ->where('approval_status', 'Approved')
            ->whereHas('schedule', function ($schedule) use ($from, $to) {
                $schedule->where('status', 'Finalized')
                    ->whereDate('schedule_date', '>=', $from->toDateString())
                    ->whereDate('schedule_date', '<=', $to->toDateString());
            });
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        return $query;
    }

    private function data(Request $request): array
    {
        [$from, $to, $status] = $this->dates($request);
        $orders = $this->baseQuery($from, $to, $status)
            ->with(['schedule', 'complaint.category', 'technicians'])
            ->get()
            ->sortBy(fn ($order) => ($order->schedule?->schedule_date?->format('Y-m-d') ?? '') . '-' . str_pad((string) $order->id, 10, '0', STR_PAD_LEFT))
            ->values();

        $total = $orders->count();
        $completed = $orders->where('status', 'Completed');
        $pending = $orders->where('status', 'Pending Workload');
        $inProgress = $orders->where('status', 'In Progress');
        $missedConfirmed = $orders->filter(fn ($order) => $order->missed_confirmed_at !== null);
        $cancelled = $orders->where('status', 'Cancelled')->count();
        $eligible = $total - $cancelled;
        $timed = $completed->filter(fn ($order) => $order->started_at && $order->completed_at && $order->completed_at->greaterThanOrEqualTo($order->started_at));
        // Compute elapsed time from actual timestamps in seconds, not rounded hours.
        // A short 1–2 minute visit must not appear as 0 working hours.
        // This measures elapsed visit time, not summed employee labour-hours.
        $durationSeconds = $timed->sum(fn ($order) => max(0, $order->started_at->diffInSeconds($order->completed_at)));
        $averageMinutes = $timed->count() ? ($durationSeconds / $timed->count() / 60) : null;
        $totalMinutes = $timed->count() ? ($durationSeconds / 60) : null;
        $timedCount = $timed->count();

        $monthLabels = [];
        $monthly = [];
        $cursor = $from->startOfMonth();
        $months = $cursor->diffInMonths($to->startOfMonth()) + 1;
        // Cap chart to 24 month buckets, while the detailed table still covers the entire range.
        $monthlyChartAvailable = $months <= 24;
        if ($monthlyChartAvailable) {
            while ($cursor->lessThanOrEqualTo($to)) {
                $key = $cursor->format('Y-m');
                $monthLabels[] = $cursor->format('M Y');
                $subset = $orders->filter(fn ($order) => $order->schedule?->schedule_date?->format('Y-m') === $key);
                $monthly[] = [
                    'label' => $cursor->format('M Y'),
                    'total' => $subset->count(),
                    'completed' => $subset->where('status', 'Completed')->count(),
                    'pending' => $subset->where('status', 'Pending Workload')->count(),
                    'missed' => $subset->filter(fn ($order) => $order->missed_confirmed_at !== null)->count(),
                ];
                $cursor = $cursor->addMonth();
            }
        }
        $statuses = $orders->groupBy('status')->map->count()->sortKeys()->all();
        $plumbers = [];
        foreach ($orders as $order) {
            foreach ($order->technicians as $plumber) {
                $id = $plumber->id;
                if (!isset($plumbers[$id])) {
                    $plumbers[$id] = ['name' => $plumber->full_name ?: ('Plumber #' . $id), 'assigned' => 0, 'completed' => 0, 'pending' => 0];
                }
                $plumbers[$id]['assigned']++;
                $plumbers[$id]['completed'] += (int) ($order->status === 'Completed');
                $plumbers[$id]['pending'] += (int) ($order->status === 'Pending Workload');
            }
        }
        $plumbers = collect($plumbers)->sortByDesc('assigned')->values();
        return compact('from', 'to', 'status', 'orders', 'total', 'completed', 'pending', 'inProgress', 'missedConfirmed', 'eligible', 'averageMinutes', 'totalMinutes', 'timedCount', 'monthly', 'monthlyChartAvailable', 'statuses', 'plumbers');
    }

    public function index(Request $request)
    {
        $data = $this->data($request);
        // Resolve the requested page before slicing the collection.
        $data['pages'] = max(1, (int) ceil($data['total'] / 15));
        $data['currentPage'] = min(max(1, (int) $request->integer('page', 1)), $data['pages']);
        $data['pageOrders'] = $data['orders']->forPage($data['currentPage'], 15);
        return view('maintenance-manager.insights.index', $data);
    }

    public function print(Request $request)
    {
        return view('maintenance-manager.insights.print', $this->data($request));
    }
}
