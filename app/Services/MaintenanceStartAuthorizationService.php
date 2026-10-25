<?php

namespace App\Services;

use App\Models\Complaint;
use App\Models\MaintenanceWorkOrder;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MaintenanceStartAuthorizationService
{
    public function authorize(
        Complaint $complaint,
        int $technicianId
    ): MaintenanceWorkOrder {
        $today = CarbonImmutable::now('Asia/Manila')->toDateString();

        $workOrder = MaintenanceWorkOrder::query()
            ->with('schedule')
            ->where('complaint_id', $complaint->id)
            ->where('status', 'Scheduled')
            ->orderByDesc('visit_number')
            ->first();

        if (!$workOrder) {
            throw ValidationException::withMessages([
                'maintenance' => 'No scheduled maintenance work order was found.',
            ]);
        }

        if (!$workOrder->schedule || $workOrder->schedule->status !== 'Finalized') {
            throw ValidationException::withMessages([
                'maintenance' => 'The Maintenance Manager must finalize the schedule before work can begin.',
            ]);
        }

        $scheduledDate = $workOrder->schedule->schedule_date->format('Y-m-d');

        if ($scheduledDate !== $today) {
            throw ValidationException::withMessages([
                'maintenance' => 'Maintenance can only begin on its approved scheduled date: '
                    . $scheduledDate . '.',
            ]);
        }

        $hasSpecificTeam = DB::table('maintenance_work_order_technicians')
            ->where('maintenance_work_order_id', $workOrder->id)
            ->exists();

        if ($hasSpecificTeam) {
            $assigned = DB::table('maintenance_work_order_technicians')
                ->where('maintenance_work_order_id', $workOrder->id)
                ->where('technician_id', $technicianId)
                ->exists();
        } else {
            // Compatibility for older work orders created before the new pivot.
            $assigned = DB::table('complaint_technicians')
                ->where('complaint_id', $complaint->id)
                ->where('technician_id', $technicianId)
                ->whereIn('status', ['Assigned', 'In Progress'])
                ->exists();
        }

        if (!$assigned) {
            throw ValidationException::withMessages([
                'maintenance' => 'You are not assigned to this maintenance work order.',
            ]);
        }

        return $workOrder;
    }
}
