<?php

namespace App\Services;

use App\Models\Complaint;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceScheduleHistory;
use App\Models\MaintenanceWorkOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BatchMaintenancePlanningService
{
    private const TIMEZONE = 'Asia/Manila';

    public function __construct(
        private MaintenancePlanningQueueService $planning,
        private MaintenanceSchedulingService $scheduling,
        private MaintenanceWorkOrderValidationService $validation
    ) {}

    public function create(array $data, int $managerId): MaintenanceSchedule
    {
        $date = $data['schedule_date'];
        $jobs = $data['jobs'] ?? [];

        $target = $this->validation->validateWorkingDate($date);

        if (empty($jobs)) {
            $this->fail('jobs', 'Select at least one complaint.');
        }

        $preparedJobs = [];
        $complaintIds = [];
        $allTechnicianIds = [];

        foreach ($jobs as $index => $job) {
            $complaintId = (int) ($job['complaint_id'] ?? 0);

            if ($complaintId <= 0) {
                $this->fail(
                    "jobs.$index.complaint_id",
                    'Invalid complaint.'
                );
            }

            if (in_array($complaintId, $complaintIds, true)) {
                $this->fail(
                    "jobs.$index.complaint_id",
                    'The same complaint cannot appear twice in one batch.'
                );
            }

            $complaintIds[] = $complaintId;

            $technicianIds = array_values(array_unique(
                array_map('intval', $job['technician_ids'] ?? [])
            ));

            sort($technicianIds);

            if (empty($technicianIds)) {
                $this->fail(
                    "jobs.$index.technician_ids",
                    'Assign at least one plumber to each complaint.'
                );
            }

            foreach ($technicianIds as $technicianId) {
                if ($technicianId <= 0) {
                    $this->fail(
                        "jobs.$index.technician_ids",
                        'Invalid plumber selection.'
                    );
                }

                $allTechnicianIds[] = $technicianId;
            }

            $preparedJobs[] = [
                'index' => $index,
                'complaint_id' => $complaintId,
                'technician_ids' => $technicianIds,
                'override_reason' => trim((string) (
                    $job['override_reason'] ?? ''
                )),
            ];
        }

        $allTechnicianIds = array_values(array_unique($allTechnicianIds));
        sort($allTechnicianIds);
        sort($complaintIds);

        return DB::transaction(function () use (
            $date,
            $target,
            $preparedJobs,
            $complaintIds,
            $allTechnicianIds,
            $managerId
        ) {
            $duties = $this->validation->lockDuties(
                $date,
                $allTechnicianIds
            );

            $complaints = Complaint::query()
                ->with(['division', 'commercialResolution'])
                ->whereIn('id', $complaintIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $schedule = MaintenanceSchedule::query()
                ->whereDate('schedule_date', $date)
                ->lockForUpdate()
                ->first();
            if (
                $schedule &&
                !in_array($schedule->status, ['Draft', 'Finalized'], true)
            ) {
                $this->fail(
                    'schedule_date',
                    'This maintenance schedule has an unsupported status.'
                );
            }

            $eligibleIds = $this->planning
                ->unassigned()
                ->whereIn('complaints.id', $complaintIds)
                ->pluck('complaints.id')
                ->map(fn($id) => (int) $id)
                ->all();

            $eligibleLookup = array_fill_keys($eligibleIds, true);

            foreach ($preparedJobs as $job) {
                $index = $job['index'];
                $complaintId = $job['complaint_id'];
                $complaint = $complaints->get($complaintId);

                if (
                    !$complaint ||
                    !isset($eligibleLookup[$complaintId])
                ) {
                    $this->fail(
                        "jobs.$index.complaint_id",
                        "Complaint #{$complaintId} is no longer available for planning."
                    );
                }

                $hasActiveWorkOrder = MaintenanceWorkOrder::query()
                    ->where('complaint_id', $complaintId)
                    ->whereIn('status', [
                        'Scheduled',
                        'In Progress',
                        'Pending Workload',
                    ])
                    ->exists();

                if ($hasActiveWorkOrder) {
                    $this->fail(
                        "jobs.$index.complaint_id",
                        "Complaint #{$complaintId} already has an active maintenance work order."
                    );
                }

                $earliest = $this->scheduling
                    ->earliestEligibleDate($complaint);

                $isEarly = $target->lt($earliest->startOfDay());

                if (
                    $isEarly &&
                    mb_strlen($job['override_reason']) < 10
                ) {
                    $this->fail(
                        "jobs.$index.override_reason",
                        "Complaint #{$complaintId} is recommended for "
                            . $earliest->toDateString()
                            . '. Provide a manager reason of at least 10 characters to schedule earlier.'
                    );
                }

                foreach ($job['technician_ids'] as $technicianId) {
                    $this->validation->validateTechnicianForDate(
                        $technicianId,
                        $date,
                        "jobs.$index.technician_ids",
                        $duties
                    );
                }
            }

            if (!$schedule) {
                $schedule = MaintenanceSchedule::create([
                    'schedule_date' => $date,
                    'status' => 'Draft',
                    'created_by' => $managerId,
                ]);
            }

            $now = now(self::TIMEZONE);

            foreach ($preparedJobs as $job) {
                $complaint = $complaints->get($job['complaint_id']);
                $technicianIds = $job['technician_ids'];

                $earliest = $this->scheduling
                    ->earliestEligibleDate($complaint);

                $isEarly = $target->lt($earliest->startOfDay());

                $visitNumber = (
                    MaintenanceWorkOrder::query()
                    ->where('complaint_id', $complaint->id)
                    ->max('visit_number') ?? 0
                ) + 1;

                $workOrder = MaintenanceWorkOrder::create([
                    'maintenance_schedule_id' => $schedule->id,
                    'complaint_id' => $complaint->id,
                    'visit_number' => $visitNumber,
                    'dispatch_type' => 'Regular',
                    'status' => 'Scheduled',
                    'planned_start_time' => null,
                    'planned_end_time' => null,
                    'scheduled_by' => $managerId,
                    'is_late_addition' => $isEarly,
                    'late_addition_reason' => $isEarly
                        ? $job['override_reason']
                        : null,
                    'approval_status' => 'Draft',
                    'approved_by' => null,
                    'approved_at' => null,
                ]);

                $workOrder->technicians()->sync(
                    collect($technicianIds)
                        ->mapWithKeys(fn($id) => [
                            $id => ['assignment_role' => 'Member'],
                        ])
                        ->all()
                );

                /*
|--------------------------------------------------------------------------
| Preserve complaint-level technician history
|--------------------------------------------------------------------------
|
| Work-order technicians represent the assigned team for this visit.
| Complaint technicians represent all technicians associated with
| the complaint, including technicians from earlier visits.
|
| Only insert missing complaint-level technicians.
|
*/

                $existingTechnicianIds = DB::table('complaint_technicians')
                    ->where('complaint_id', $complaint->id)
                    ->pluck('technician_id')
                    ->map(fn($id) => (int) $id)
                    ->all();

                foreach ($technicianIds as $technicianId) {
                    if (in_array($technicianId, $existingTechnicianIds, true)) {
                        continue;
                    }

                    DB::table('complaint_technicians')->insert([
                        'complaint_id' => $complaint->id,
                        'technician_id' => $technicianId,
                        'status' => 'Assigned',
                        'assigned_at' => $now,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }

                $complaint->update([
                    'status' => 'Assigned',
                ]);

                MaintenanceScheduleHistory::create([
                    'maintenance_work_order_id' => $workOrder->id,
                    'performed_by' => $managerId,
                    'action' => $schedule->status === 'Finalized'
                        ? 'Draft Addition'
                        : ($isEarly
                            ? 'Manager Early Assignment'
                            : 'Batch Scheduled'),
                    'old_values' => null,
                    'new_values' => [
                        'schedule_date' => $date,
                        'technician_ids' => $technicianIds,
                        'schedule_status' => $schedule->status,
                        'approval_status' => 'Draft',
                        'recommended_date' => $earliest->toDateString(),
                        'planned_start_time' => null,
                        'planned_end_time' => null,
                    ],
                    'reason' => $isEarly
                        ? $job['override_reason']
                        : 'Assigned through daily maintenance workload planning.',
                ]);
            }

            return $schedule->fresh();
        }, 3);
    }

    private function fail(string $field, string $message): never
    {
        throw ValidationException::withMessages([
            $field => $message,
        ]);
    }
}
