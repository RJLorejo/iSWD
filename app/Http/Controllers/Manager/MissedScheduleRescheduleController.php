<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceScheduleHistory;
use App\Models\MaintenanceWorkOrder;
use App\Models\User;
use App\Services\MaintenanceWorkOrderValidationService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MissedScheduleRescheduleController extends Controller
{
    public function edit(MaintenanceWorkOrder $workOrder)
    {
        $workOrder->load(['complaint.category', 'schedule', 'technicians']);
        if (!$this->isMissed($workOrder)) {
            return redirect()->route('maintenance-manager.backlogs.index')
                ->with('success', 'This visit is no longer awaiting missed-schedule review. Check its replacement in Daily Maintenance Planning.');
        }

        $suggestedDate = CarbonImmutable::now('Asia/Manila')->addDay()->startOfDay();
        while ($suggestedDate->isWeekend()) {
            $suggestedDate = $suggestedDate->addDay();
        }

        $plumbers = User::role('Maintenance Technician')
            ->where('is_active', true)
            ->orderBy('first_name')->get();

        return view('maintenance-manager.backlogs.reschedule', compact('workOrder', 'suggestedDate', 'plumbers'));
    }

    public function store(Request $request, MaintenanceWorkOrder $workOrder, MaintenanceWorkOrderValidationService $validation)
    {
        $data = $request->validate([
            'schedule_date' => ['required', 'date_format:Y-m-d'],
            'technician_ids' => ['required', 'array', 'min:1'],
            'technician_ids.*' => ['required', 'integer', 'distinct', 'exists:users,id'],
            'missed_manager_reason' => ['required', 'string', 'min:10', 'max:2000'],
            'manager_remarks' => ['nullable', 'string', 'max:2000'],
            'confirm_no_work' => ['accepted'],
        ]);

        $date = $validation->validateWorkingDate($data['schedule_date'])->toDateString();
        $ids = array_map('intval', $data['technician_ids']);
        sort($ids);

        DB::transaction(function () use ($workOrder, $validation, $data, $date, $ids, $request) {
            // Serialize competing visits for the same complaint, not merely this work order.
            $originalComplaintId = (int) $workOrder->complaint_id;
            $complaint = Complaint::query()->lockForUpdate()->findOrFail($originalComplaintId);
            $original = MaintenanceWorkOrder::query()->lockForUpdate()->findOrFail($workOrder->id);
            $original->load('schedule');
            $this->assertMissed($original);

            if ($original->participations()->exists()) {
                throw ValidationException::withMessages(['work_order' => 'Participation records exist. Verify whether work occurred before declaring this visit missed.']);
            }

            if (in_array($complaint->status, ['Completed', 'Closed', 'Rejected'], true)) {
                throw ValidationException::withMessages(['work_order' => 'This complaint is no longer eligible for rescheduling.']);
            }

            if (MaintenanceWorkOrder::query()->where('rescheduled_from_work_order_id', $original->id)->exists()) {
                throw ValidationException::withMessages(['work_order' => 'A replacement visit already exists.']);
            }

            $newerActive = MaintenanceWorkOrder::query()
                ->where('complaint_id', $complaint->id)
                ->where('id', '!=', $original->id)
                ->whereNotIn('status', ['Cancelled', 'Rescheduled'])
                ->exists();
            if ($newerActive) {
                throw ValidationException::withMessages(['work_order' => 'Another active visit exists for this complaint. Review it before rescheduling.']);
            }

            $duties = $validation->lockDuties($date, $ids);
            foreach ($ids as $id) {
                $validation->validateTechnicianForDate($id, $date, 'technician_ids', $duties);
            }

            $schedule = MaintenanceSchedule::query()
                ->whereDate('schedule_date', $date)
                ->lockForUpdate()
                ->first();
            if (!$schedule) {
                // A unique index on maintenance_schedules.schedule_date is recommended.
                $schedule = MaintenanceSchedule::create([
                    'schedule_date' => $date,
                    'status' => 'Draft',
                    'created_by' => $request->user()->id,
                ]);
            }

            $visit = (int) MaintenanceWorkOrder::query()
                ->where('complaint_id', $complaint->id)
                ->max('visit_number') + 1;

            $replacement = MaintenanceWorkOrder::create([
                'maintenance_schedule_id' => $schedule->id,
                'complaint_id' => $complaint->id,
                'visit_number' => $visit,
                'dispatch_type' => $original->dispatch_type,
                'status' => 'Scheduled',
                'approval_status' => 'Draft',
                'scheduled_by' => $request->user()->id,
                'manager_remarks' => trim($data['manager_remarks'] ?? ''),
                'is_late_addition' => $schedule->status === 'Finalized',
                'late_addition_reason' => $schedule->status === 'Finalized'
                    ? 'Replacement of missed visit, work order #'.$original->id
                    : null,
                'rescheduled_from_work_order_id' => $original->id,
            ]);

            $pivot = [];
            foreach ($ids as $i => $id) {
                $pivot[$id] = ['assignment_role' => 'Member'];
            }
            $replacement->technicians()->sync($pivot);

            $now = now('Asia/Manila');
            $original->update([
                'status' => 'Rescheduled',
                'missed_manager_reason' => trim($data['missed_manager_reason']),
                'missed_confirmed_by' => $request->user()->id,
                'missed_confirmed_at' => $now,
            ]);

            MaintenanceScheduleHistory::create([
                'maintenance_work_order_id' => $original->id,
                'performed_by' => $request->user()->id,
                'action' => 'Rescheduled',
                'old_values' => [
                    'status' => 'Scheduled',
                    'technician_reported_reason' => $original->missed_reason,
                    'manager_verified_reason' => null,
                    'schedule_date' => $original->schedule->schedule_date->format('Y-m-d'),
                    'visit_number' => $original->visit_number,
                ],
                'new_values' => [
                    'status' => 'Rescheduled',
                    'replacement_work_order_id' => $replacement->id,
                    'replacement_visit_number' => $visit,
                    'new_schedule_date' => $date,
                    'approval_status' => 'Draft',
                    'manager_verified_reason' => trim($data['missed_manager_reason']),
                ],
                'reason' => trim($data['missed_manager_reason']),
            ]);
        }, 3);

        return redirect()->route('maintenance-manager.schedules.index', ['date' => $date])
            ->with('success', 'Missed visit confirmed. A replacement Draft work order was created. Finalize or approve its addition before dispatch.');
    }

    private function isMissed(MaintenanceWorkOrder $order): bool
    {
        $date = $order->schedule?->schedule_date;
        return ($order->status !== 'Scheduled'
            || $order->approval_status !== 'Approved'
            || $order->started_at !== null
            || $order->completed_at !== null
            || $order->schedule?->status !== 'Finalized'
            || !$date
            || !$date->startOfDay()->lt(CarbonImmutable::now('Asia/Manila')->startOfDay())) ? false : true;
    }

    private function assertMissed(MaintenanceWorkOrder $order): void
    {
        if (!$this->isMissed($order)) {
            throw ValidationException::withMessages(['work_order' => 'This work order is no longer eligible for missed-visit rescheduling.']);
        }
    }
}
