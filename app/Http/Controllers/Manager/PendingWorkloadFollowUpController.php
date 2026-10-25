<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceScheduleHistory;
use App\Models\MaintenanceWorkOrder;
use App\Services\MaintenanceWorkOrderValidationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PendingWorkloadFollowUpController extends Controller
{
    public function store(
        Request $request,
        MaintenanceWorkOrder $workOrder,
        MaintenanceWorkOrderValidationService $validation
    ) {
        $data = $request->validate([
            'schedule_date' => ['required', 'date_format:Y-m-d'],
            'technician_ids' => ['required', 'array', 'min:1'],
            'technician_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:users,id',
            ],
            'manager_remarks' => ['nullable', 'string', 'max:2000'],
        ]);

        $date = $data['schedule_date'];
        $managerId = (int) $request->user()->id;

        // Reuse the same working-date rules as Daily Planning.
        $validation->validateWorkingDate($date);

        $technicianIds = array_map(
            'intval',
            $data['technician_ids']
        );

        $technicianIds = array_values(
            array_unique($technicianIds)
        );

        sort($technicianIds);

        DB::transaction(function () use (
            $workOrder,
            $validation,
            $managerId,
            $date,
            $technicianIds,
            $data
        ) {
            $original = MaintenanceWorkOrder::query()
                ->lockForUpdate()
                ->findOrFail($workOrder->id);

            $original->load('schedule');

            if ($original->status !== 'Pending Workload') {
                throw ValidationException::withMessages([
                    'work_order' =>
                        'This work order is no longer pending.',
                ]);
            }

            if (
                !filled($original->pending_reason) ||
                !$original->pending_recorded_at
            ) {
                throw ValidationException::withMessages([
                    'work_order' =>
                        'A technician-submitted pending reason is required.',
                ]);
            }

            // The follow-up must be after the unfinished visit.
            $originalDate = $original->schedule?->schedule_date;

            if (!$originalDate) {
                throw ValidationException::withMessages([
                    'schedule_date' =>
                        'The original maintenance visit has no valid schedule date.',
                ]);
            }

            $originalDay = \Carbon\CarbonImmutable::parse(
                $originalDate,
                'Asia/Manila'
            )->toDateString();

            if ($date < $originalDay) {
                throw ValidationException::withMessages([
                    'schedule_date' =>
                        'The follow-up date cannot be earlier than the original maintenance visit.',
                ]);
            }

            $complaint = Complaint::query()
                ->lockForUpdate()
                ->findOrFail($original->complaint_id);

            if (!in_array($complaint->status, ['Assigned', 'In Progress'], true)) {
                throw ValidationException::withMessages([
                    'work_order' =>
                        'The complaint is no longer eligible for follow-up planning.',
                ]);
            }

            // Prevent duplicate active follow-up visits.
            $alreadyPlanned = MaintenanceWorkOrder::query()
                ->where('complaint_id', $complaint->id)
                ->where('id', '>', $original->id)
                ->whereNotIn('status', [
                    'Cancelled',
                    'Rescheduled',
                ])
                ->exists();

            if ($alreadyPlanned) {
                throw ValidationException::withMessages([
                    'work_order' =>
                        'A follow-up work order already exists. Review the existing schedule.',
                ]);
            }

            // Check whether the destination day is still Draft.
            $schedule = MaintenanceSchedule::query()
                ->whereDate('schedule_date', $date)
                ->lockForUpdate()
                ->first();

            if ($schedule && !in_array($schedule->status, ['Draft', 'Finalized'], true)) {
                throw ValidationException::withMessages([
                    'schedule_date' =>
                        'The selected schedule cannot accept additional work orders.',
                ]);
            }

            // Lock and validate the actual duty records.
            $duties = $validation->lockDuties(
                $date,
                $technicianIds
            );

            foreach ($technicianIds as $technicianId) {
                $validation->validateTechnicianForDate(
                    $technicianId,
                    $date,
                    'technician_ids',
                    $duties
                );
            }

            if (!$schedule) {
                $schedule = MaintenanceSchedule::create([
                    'schedule_date' => $date,
                    'status' => 'Draft',
                    'created_by' => $managerId,
                ]);
            }

            // Complaint is locked, so follow-up visit numbering
            // is serialized for this complaint.
            $visitNumber = (int) MaintenanceWorkOrder::query()
                ->where('complaint_id', $complaint->id)
                ->max('visit_number') + 1;

            $followUp = MaintenanceWorkOrder::create([
                'maintenance_schedule_id' => $schedule->id,
                'complaint_id' => $complaint->id,
                'visit_number' => $visitNumber,
                'dispatch_type' => 'Regular',
                'status' => 'Scheduled',
                'approval_status' => 'Draft',
                'planned_start_time' => null,
                'planned_end_time' => null,
                'scheduled_by' => $managerId,
                'manager_remarks' =>
                    $data['manager_remarks'] ?? null,
            ]);

            // Assign the team to this new visit only.
            $assignments = [];

            foreach ($technicianIds as $technicianId) {
                $assignments[$technicianId] = [
                    'assignment_role' => 'Member',
                ];
            }

            $followUp->technicians()->sync($assignments);

            /*
             * Preserve existing complaint technician records.
             *
             * Do not overwrite earlier start timestamps,
             * completion timestamps, or historical assignments.
             */
            $existingIds = DB::table('complaint_technicians')
                ->where('complaint_id', $complaint->id)
                ->pluck('technician_id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $now = now('Asia/Manila');

            foreach ($technicianIds as $technicianId) {
                if (!in_array(
                    $technicianId,
                    $existingIds,
                    true
                )) {
                    DB::table('complaint_technicians')->insert([
                        'complaint_id' => $complaint->id,
                        'technician_id' => $technicianId,
                        'status' => 'Assigned',
                        'assigned_at' => $now,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }

            // Audit event on the original unfinished visit.
            MaintenanceScheduleHistory::create([
                'maintenance_work_order_id' => $original->id,
                'performed_by' => $managerId,
                'action' => 'Follow-up Planned',
                'old_values' => [
                    'status' => 'Pending Workload',
                    'pending_reason' =>
                        $original->pending_reason,
                    'visit_number' =>
                        $original->visit_number,
                ],
                'new_values' => [
                    'follow_up_work_order_id' =>
                        $followUp->id,
                    'follow_up_visit_number' =>
                        $followUp->visit_number,
                    'schedule_date' => $date,
                    'schedule_status' => $schedule->status,
                    'technician_ids' => $technicianIds,
                ],
                'reason' =>
                    'Manager created a follow-up plan for unfinished maintenance.',
            ]);

            // Audit event on the newly created follow-up visit.
            MaintenanceScheduleHistory::create([
                'maintenance_work_order_id' => $followUp->id,
                'performed_by' => $managerId,
                'action' => 'Follow-up Work Order Created',
                'old_values' => [
                    'source_pending_work_order_id' =>
                        $original->id,
                ],
                'new_values' => [
                    'status' => 'Scheduled',
                    'schedule_status' => $schedule->status,
                    'schedule_date' => $date,
                    'planned_start_time' => null,
                    'planned_end_time' => null,
                    'technician_ids' => $technicianIds,
                ],
                'reason' =>
                    $data['manager_remarks']
                    ?? 'Follow-up maintenance planned by the manager.',
            ]);
        }, 3);

        return redirect()
            ->route('maintenance-manager.schedules.index', [
                'date' => $date,
            ])
            ->with(
                'success',
                'Follow-up created as Draft. Approve it through Daily Maintenance Planning before the replacement plumber can start.'
            );
    }
}
