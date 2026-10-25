<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceScheduleHistory;
use App\Models\MaintenanceWorkOrder;
use App\Models\MaintenanceWorkOrderParticipation;
use App\Services\MaintenanceWorkOrderValidationService;
use App\Services\TechnicianAvailabilityService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MaintenanceHandoverController extends Controller
{
    public function edit(MaintenanceWorkOrder $workOrder)
    {
        $workOrder->load(['complaint', 'schedule', 'technicians', 'participations.technician']);
        $this->validateInProgress($workOrder);

        $date = $workOrder->schedule->schedule_date->format('Y-m-d');
        $eligible = app(TechnicianAvailabilityService::class)
            ->availableOn($date)
            ->unique('technician_id')
            ->filter(fn ($duty) => !$workOrder->technicians->contains('id', $duty->technician_id))
            ->values();

        return view('maintenance-manager.schedules.handover', compact('workOrder', 'eligible', 'date'));
    }

    public function update(
        Request $request,
        MaintenanceWorkOrder $workOrder,
        MaintenanceWorkOrderValidationService $validation
    ) {
        $data = $request->validate([
            'old_technician_id' => ['required', 'integer', 'exists:users,id'],
            'new_technician_id' => ['required', 'integer', 'different:old_technician_id', 'exists:users,id'],
            'reason' => ['required', 'string', 'min:10', 'max:2000'],
            'contribution_notes' => ['required', 'string', 'min:10', 'max:3000'],
        ]);

        $date = DB::transaction(function () use ($request, $workOrder, $validation, $data) {
            $order = MaintenanceWorkOrder::query()->lockForUpdate()->findOrFail($workOrder->id);
            $schedule = MaintenanceSchedule::query()
                ->lockForUpdate()->findOrFail($order->maintenance_schedule_id);
            $order->setRelation('schedule', $schedule);
            $this->validateInProgress($order);

            $date = $schedule->schedule_date->format('Y-m-d');
            if ($date !== now('Asia/Manila')->toDateString()) {
                throw ValidationException::withMessages([
                    'work_order' => 'In-progress handover is only allowed on the scheduled maintenance day. Contact management to resolve older active visits.',
                ]);
            }

            $oldId = (int) $data['old_technician_id'];
            $newId = (int) $data['new_technician_id'];

            $teamRoles = DB::table('maintenance_work_order_technicians')
                ->where('maintenance_work_order_id', $order->id)
                ->lockForUpdate()
                ->pluck('assignment_role', 'technician_id')
                ->all();

            if (!array_key_exists($oldId, $teamRoles)) {
                throw ValidationException::withMessages([
                    'old_technician_id' => 'This plumber is no longer on the current team.',
                ]);
            }
            if (array_key_exists($newId, $teamRoles)) {
                throw ValidationException::withMessages([
                    'new_technician_id' => 'This plumber is already assigned to the work order.',
                ]);
            }

            $validation->validateWorkingDate($date);
            $duties = $validation->lockDuties($date, [$newId]);
            $validation->validateTechnicianForDate($newId, $date, 'new_technician_id', $duties);

            $now = now('Asia/Manila');
            $oldParticipation = MaintenanceWorkOrderParticipation::query()
                ->where('maintenance_work_order_id', $order->id)
                ->where('technician_id', $oldId)
                ->lockForUpdate()->first();

            // A handover requires actual work by the outgoing plumber, not just
            // membership in the team. No automatic credit for nonparticipants.
            if (!$oldParticipation || $oldParticipation->status !== 'Active'
                || !$oldParticipation->started_at) {
                throw ValidationException::withMessages([
                    'old_technician_id' => 'No active participation was recorded for this plumber. A manager cannot certify unrecorded work. Use the correct workflow after verifying actual participation.',
                ]);
            }

            $oldParticipation->update([
                'status' => 'Handed Over',
                'ended_at' => $now,
                'contribution_notes' => trim($data['contribution_notes']),
                'recorded_by' => $request->user()->id,
            ]);

            $oldTeam = array_map('intval', array_keys($teamRoles));
            sort($oldTeam);
            $role = $teamRoles[$oldId];
            unset($teamRoles[$oldId]);
            $teamRoles[$newId] = $role;
            $sync = [];
            foreach ($teamRoles as $id => $assignmentRole) {
                $sync[(int) $id] = ['assignment_role' => $assignmentRole];
            }
            $order->technicians()->sync($sync);

            // Replacement is authorized to continue but receives no participation
            // credit until an actual technician action is recorded.
            $exists = DB::table('complaint_technicians')
                ->where('complaint_id', $order->complaint_id)
                ->where('technician_id', $newId)->exists();
            if (!$exists) {
                DB::table('complaint_technicians')->insert([
                    'complaint_id' => $order->complaint_id,
                    'technician_id' => $newId,
                    'status' => 'Assigned',
                    'assigned_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $newTeam = array_map('intval', array_keys($teamRoles));
            sort($newTeam);

            MaintenanceScheduleHistory::create([
                'maintenance_work_order_id' => $order->id,
                'performed_by' => $request->user()->id,
                'action' => 'Maintenance Handover',
                'old_values' => [
                    'status' => 'In Progress',
                    'technician_ids' => $oldTeam,
                    'outgoing_technician_id' => $oldId,
                ],
                'new_values' => [
                    'status' => 'In Progress',
                    'technician_ids' => $newTeam,
                    'incoming_technician_id' => $newId,
                    'handover_at' => $now->toDateTimeString(),
                ],
                'reason' => $data['reason'],
            ]);

            return $date;
        }, 3);

        return redirect()
            ->route('maintenance-manager.schedules.index', ['date' => $date])
            ->with('success', 'Maintenance handover recorded. The replacement plumber can continue the approved work order.');
    }

    private function validateInProgress(MaintenanceWorkOrder $order): void
    {
        if ($order->status !== 'In Progress' || !$order->started_at
            || $order->completed_at || $order->approval_status !== 'Approved'
            || !$order->schedule || $order->schedule->status !== 'Finalized') {
            throw ValidationException::withMessages([
                'work_order' => 'Only an approved, started, in-progress work order on a finalized schedule can be handed over.',
            ]);
        }
    }
}
