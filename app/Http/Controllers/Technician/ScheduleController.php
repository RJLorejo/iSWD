<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceWorkOrder;
use App\Models\TechnicianDuty;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $technicianId = (int) Auth::id();

        $today = now('Asia/Manila')->toDateString();

        // The technician's daily duty is separate from individual repair visits.
        $todayDuty = TechnicianDuty::query()
            ->with('technician.serviceArea')
            ->where('technician_id', $technicianId)
            ->whereDate('duty_date', $today)
            ->first();

        $nextDuties = TechnicianDuty::query()
            ->where('technician_id', $technicianId)
            ->whereDate('duty_date', '>', $today)
            ->orderBy('duty_date')
            ->limit(5)
            ->get();

        $request->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'view' => ['nullable', 'in:upcoming,history,missed'],
        ]);

        $view = $request->query('view', 'upcoming');

        /*
        |--------------------------------------------------------------------------
        | AUTHORIZED TECHNICIAN WORK ORDERS
        |--------------------------------------------------------------------------
        */

        $baseQuery = function () use ($technicianId): Builder {
            return MaintenanceWorkOrder::query()
                ->whereHas('technicians', function (Builder $query) use ($technicianId) {
                    $query->where('users.id', $technicianId);
                })
                ->whereHas('schedule', function (Builder $query) {
                    $query->whereIn('maintenance_schedules.status', ['Draft', 'Finalized']);
                });
        };

        /*
        |--------------------------------------------------------------------------
        | DASHBOARD STATISTICS
        |--------------------------------------------------------------------------
        */


        $todayCount = $baseQuery()
            ->where('maintenance_work_orders.approval_status', 'Approved')
            ->whereHas('schedule', function (Builder $query) use ($today) {
                $query->whereDate(
                    'maintenance_schedules.schedule_date',
                    $today
                );
            })
            ->whereNotIn('maintenance_work_orders.status', [
                'Completed',
                'Pending Workload',
            ])
            ->count();


        $upcomingCount = $baseQuery()
            ->where('maintenance_work_orders.approval_status', 'Approved')
            ->whereHas('schedule', function (Builder $query) use ($today) {
                $query->whereDate(
                    'maintenance_schedules.schedule_date',
                    '>=',
                    $today
                );
            })
            ->whereNotIn('maintenance_work_orders.status', [
                'Completed',
                'Pending Workload',
            ])
            ->count();


        $missedCount = $baseQuery()
            ->where('maintenance_work_orders.approval_status', 'Approved')
            ->where('maintenance_work_orders.status', 'Scheduled')
            ->whereNull('maintenance_work_orders.started_at')
            ->whereHas('schedule', fn (Builder $q) => $q
                ->where('maintenance_schedules.status', 'Finalized')
                ->whereDate('maintenance_schedules.schedule_date', '<', $today))
            ->count();

        $draftCount = $baseQuery()
            ->where('maintenance_work_orders.approval_status', 'Draft')
            ->where('maintenance_work_orders.status', 'Scheduled')
            ->whereHas('schedule', fn (Builder $q) => $q->whereDate('schedule_date', '>=', $today))
            ->count();

        $completedCount = $baseQuery()
            ->where('maintenance_work_orders.approval_status', 'Approved')
            ->where(
                'maintenance_work_orders.status',
                'Completed'
            )
            ->count();

        /*
        |--------------------------------------------------------------------------
        | SCHEDULE LIST
        |--------------------------------------------------------------------------
        */

        $query = $baseQuery()
            ->with([
                'schedule',
                'complaint.consumer',
                'complaint.category',
                'complaint.aiAnalysis',
                'technicians',
            ]);

        if ($view === 'missed') {
            $query->where('maintenance_work_orders.approval_status', 'Approved')
                ->where('maintenance_work_orders.status', 'Scheduled')
                ->whereNull('maintenance_work_orders.started_at')
                ->whereHas('schedule', fn (Builder $q) => $q
                    ->where('maintenance_schedules.status', 'Finalized')
                    ->whereDate('maintenance_schedules.schedule_date', '<', $today));
        } elseif ($view === 'history') {

            $query->where(
                'maintenance_work_orders.status',
                'Completed'
            );
        } else {

            $query
                ->whereNotIn('maintenance_work_orders.status', [
                    'Completed',
                    'Pending Workload',
                ])
                ->whereHas('schedule', function (Builder $query) use ($today) {
                    $query->whereDate(
                        'maintenance_schedules.schedule_date',
                        '>=',
                        $today
                    );
                });
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
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('complainant_name', 'like', "%{$search}%")
                    ->orWhereHas('category', function (Builder $categoryQuery) use ($search) {
                        $categoryQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('consumer', function (Builder $consumerQuery) use ($search) {
                        $consumerQuery
                            ->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('account_number', 'like', "%{$search}%");
                    });
            });
        }

        /*
        |--------------------------------------------------------------------------
        | DATE FILTERS
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date_from')) {
            $query->whereHas('schedule', function (Builder $scheduleQuery) use ($request) {
                $scheduleQuery->whereDate(
                    'schedule_date',
                    '>=',
                    $request->date_from
                );
            });
        }

        if ($request->filled('date_to')) {
            $query->whereHas('schedule', function (Builder $scheduleQuery) use ($request) {
                $scheduleQuery->whereDate(
                    'schedule_date',
                    '<=',
                    $request->date_to
                );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | SORTING
        |--------------------------------------------------------------------------
        |
        | Upcoming: earliest schedule date and start time first.
        | History: most recent completed visits first.
        |
        */

        $query->join(
            'maintenance_schedules',
            'maintenance_schedules.id',
            '=',
            'maintenance_work_orders.maintenance_schedule_id'
        )
            ->select('maintenance_work_orders.*');

        if (in_array($view, ['history', 'missed'], true)) {
            $query
                ->orderByDesc('maintenance_schedules.schedule_date')
                ->orderByDesc('maintenance_work_orders.planned_start_time')
                ->orderByDesc('maintenance_work_orders.id');
        } else {
            $query
                ->orderBy('maintenance_schedules.schedule_date')
                ->orderByRaw(
                    'maintenance_work_orders.planned_start_time IS NULL ASC'
                )
                ->orderBy('maintenance_work_orders.planned_start_time')
                ->orderBy('maintenance_work_orders.id');
        }

        $workOrders = $query
            ->paginate(10)
            ->withQueryString();

        return view('technician.schedule.index', compact(
            'workOrders',
            'todayCount',
            'upcomingCount',
            'completedCount',
            'missedCount',
            'draftCount',
            'todayDuty',
            'nextDuties',
            'view'
        ));
    }

    // Add this method INSIDE App\Http\Controllers\Technician\ScheduleController,
    // immediately before the class's final closing brace.
    public function print(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'view' => ['nullable', 'in:upcoming,history,missed'],
        ]);

        $technicianId = (int) Auth::id();
        $today = now('Asia/Manila')->toDateString();
        $view = $validated['view'] ?? 'upcoming';

        $query = MaintenanceWorkOrder::query()
            ->whereHas('technicians', function (Builder $team) use ($technicianId) {
                $team->where('users.id', $technicianId);
            })
            ->whereHas('schedule', function (Builder $schedule) {
                $schedule->where('maintenance_schedules.status', 'Finalized');
            })
            ->with(['schedule', 'complaint.consumer', 'complaint.category', 'complaint.aiAnalysis']);

        if ($view === 'missed') {
            $query->where('maintenance_work_orders.approval_status', 'Approved')
                ->where('maintenance_work_orders.status', 'Scheduled')
                ->whereNull('maintenance_work_orders.started_at')
                ->whereHas('schedule', fn (Builder $q) => $q
                    ->where('maintenance_schedules.status', 'Finalized')
                    ->whereDate('maintenance_schedules.schedule_date', '<', $today));
        } elseif ($view === 'history') {
            $query->where('maintenance_work_orders.status', 'Completed');
        } else {
            $query->whereNotIn('maintenance_work_orders.status', ['Completed', 'Pending Workload'])
                ->whereHas('schedule', function (Builder $schedule) use ($today) {
                    $schedule->whereDate('maintenance_schedules.schedule_date', '>=', $today);
                });
        }

        if (!empty($validated['search'])) {
            $search = trim($validated['search']);
            $query->whereHas('complaint', function (Builder $complaint) use ($search) {
                $complaint->where(function (Builder $matches) use ($search) {
                    $matches->where('complaint_no', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%")
                        ->orWhere('complainant_name', 'like', "%{$search}%")
                        ->orWhereHas('category', function (Builder $category) use ($search) {
                            $category->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('consumer', function (Builder $consumer) use ($search) {
                            $consumer->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('account_number', 'like', "%{$search}%");
                        });
                });
            });
        }

        if (!empty($validated['date_from'])) {
            $from = $validated['date_from'];
            $query->whereHas('schedule', function (Builder $schedule) use ($from) {
                $schedule->whereDate('maintenance_schedules.schedule_date', '>=', $from);
            });
        }
        if (!empty($validated['date_to'])) {
            $to = $validated['date_to'];
            $query->whereHas('schedule', function (Builder $schedule) use ($to) {
                $schedule->whereDate('maintenance_schedules.schedule_date', '<=', $to);
            });
        }

        $query->join('maintenance_schedules', 'maintenance_schedules.id', '=', 'maintenance_work_orders.maintenance_schedule_id')
            ->select('maintenance_work_orders.*');

        if (in_array($view, ['history', 'missed'], true)) {
            $query->orderByDesc('maintenance_schedules.schedule_date')
                ->orderByDesc('maintenance_work_orders.planned_start_time')
                ->orderByDesc('maintenance_work_orders.id');
        } else {
            $query->orderBy('maintenance_schedules.schedule_date')
                ->orderByRaw('maintenance_work_orders.planned_start_time IS NULL ASC')
                ->orderBy('maintenance_work_orders.planned_start_time')
                ->orderBy('maintenance_work_orders.id');
        }

        $workOrders = $query->get();

        return view('technician.schedule.print', compact('workOrders', 'view', 'validated'));
    }
    /** Record a plumber explanation only; manager confirms missed visit separately. */
    public function reportMissed(Request $request, MaintenanceWorkOrder $workOrder)
    {
        $data = $request->validate([
            'missed_reason' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $workOrder, $data) {
            $order = MaintenanceWorkOrder::query()->lockForUpdate()->findOrFail($workOrder->id);
            $today = now('Asia/Manila')->toDateString();

            $assigned = $order->technicians()
                ->where('users.id', (int) $request->user()->id)
                ->exists();

            $order->load('schedule');
            if (!$assigned || $order->approval_status !== 'Approved'
                || $order->status !== 'Scheduled' || $order->started_at !== null
                || $order->schedule?->status !== 'Finalized'
                || !$order->schedule?->schedule_date?->lt(\Carbon\CarbonImmutable::parse($today, 'Asia/Manila'))
            ) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'missed_reason' => 'This visit is no longer eligible for a missed-visit report.',
                ]);
            }
            if ($order->participations()->exists()) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'missed_reason' => 'Participation is already recorded. Contact the manager to verify this visit.',
                ]);
            }
            if ($order->missed_reported_at !== null) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'missed_reason' => 'A missed-visit report has already been submitted.',
                ]);
            }

            $order->update([
                'missed_reason' => trim($data['missed_reason']),
                'missed_reported_by' => (int) $request->user()->id,
                'missed_reported_at' => now('Asia/Manila'),
            ]);
            \App\Models\MaintenanceScheduleHistory::create([
                'maintenance_work_order_id' => $order->id,
                'performed_by' => (int) $request->user()->id,
                'action' => 'Missed Visit Reported',
                'old_values' => ['missed_reported_at' => null],
                'new_values' => ['missed_reported_at' => $order->missed_reported_at?->toDateTimeString()],
                'reason' => trim($data['missed_reason']),
            ]);
        }, 3);

        return redirect()->route('technician.schedule.index', ['view' => 'missed'])
            ->with('success', 'Explanation submitted. The Maintenance Manager will review the visit.');
    }

}
