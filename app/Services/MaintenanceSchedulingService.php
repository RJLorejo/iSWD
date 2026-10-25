<?php

namespace App\Services;

use App\Models\Complaint;
use App\Models\MaintenanceSchedule;
use App\Models\MaintenanceScheduleHistory;
use App\Models\MaintenanceWorkOrder;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MaintenanceSchedulingService
{
    private const TIMEZONE = 'Asia/Manila';

    public function __construct(
        private readonly MaintenanceWorkOrderValidationService $validation
    ) {
    }

    /**
     * Calculate the earliest eligible maintenance date.
     *
     * Engineering complaints:
     * Based on the verification date.
     *
     * Commercial complaints:
     * Based on forwarding to maintenance.
     *
     * Working days: Monday to Friday.
     */
    public function earliestEligibleDate(
        Complaint $complaint
    ): CarbonImmutable {
        $complaint->loadMissing(
            'division',
            'commercialResolution'
        );

        $isEngineering = str_contains(
            strtolower(
                trim((string) $complaint->division?->name)
            ),
            'engineering'
        );

        if (
            $isEngineering &&
            $complaint->verified_at
        ) {
            $readyAt = $complaint->verified_at;
        } elseif (
            $complaint->commercialResolution
                ?->forwarded_to_maintenance_at
        ) {
            $readyAt = $complaint
                ->commercialResolution
                ->forwarded_to_maintenance_at;
        } else {
            $this->fail(
                'complaint_id',
                'This complaint does not have a valid maintenance verification or forwarding date.'
            );
        }

        $ready = $readyAt instanceof \DateTimeInterface
            ? CarbonImmutable::instance($readyAt)
                ->setTimezone(self::TIMEZONE)
            : CarbonImmutable::parse(
                $readyAt,
                self::TIMEZONE
            );

        /*
         * Readiness after 5 PM enters
         * the next working day's queue cycle.
         */
        if ($ready->format('H:i:s') > '17:00:00') {
            $ready = $this->nextWorkingDay($ready);
        }

        return $this->nextWorkingDay($ready);
    }

    /**
     * Return the next Monday-Friday working day.
     */
    public function nextWorkingDay(
        CarbonImmutable $date
    ): CarbonImmutable {
        $date = $date->startOfDay()->addDay();

        while ($date->isWeekend()) {
            $date = $date->addDay();
        }

        return $date;
    }

    /**
     * Schedule an individual complaint.
     *
     * Draft schedule:
     * - Creates a Draft work order.
     * - Requires normal daily schedule finalization.
     *
     * Finalized schedule:
     * - Allows new complaints on an eligible working date.
     * - Creates a Draft Addition.
     * - Does not modify existing approved work orders.
     * - Requires separate manager approval.
     */
    public function scheduleComplaint(
        int $complaintId,
        string $date,
        int $managerId,
        bool $lateOverride = false,
        ?string $reason = null
    ): MaintenanceWorkOrder {
        return DB::transaction(function () use (
            $complaintId,
            $date,
            $managerId,
            $lateOverride,
            $reason
        ) {
            /*
            |--------------------------------------------------------------------------
            | 1. Validate Working Date
            |--------------------------------------------------------------------------
            */

            $targetDate = $this->validation
                ->validateWorkingDate(
                    $date,
                    'schedule_date'
                );

            $dateString = $targetDate->toDateString();

            /*
            |--------------------------------------------------------------------------
            | 2. Lock Complaint
            |--------------------------------------------------------------------------
            */

            $complaint = Complaint::query()
                ->lockForUpdate()
                ->findOrFail($complaintId);

            if ($complaint->status !== 'Assigned') {
                $this->fail(
                    'complaint_id',
                    'Only assigned complaints that have not started maintenance can be scheduled.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 3. Get Assigned Maintenance Team
            |--------------------------------------------------------------------------
            */

            $technicianIds = $complaint
                ->technicians()
                ->pluck('users.id')
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->sort()
                ->values()
                ->all();

            if (empty($technicianIds)) {
                $this->fail(
                    'complaint_id',
                    'Assign at least one maintenance technician before scheduling.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 4. Validate Recommended Maintenance Date
            |--------------------------------------------------------------------------
            */

            $earliest = $this->earliestEligibleDate(
                $complaint
            );

            $isEarly = $targetDate->lt($earliest);

            /*
             * A finalized schedule does not automatically
             * require an override.
             *
             * An override is required only when scheduling
             * earlier than the recommended date.
             */
            if ($isEarly && !$lateOverride) {
                $this->fail(
                    'schedule_date',
                    'Earliest eligible date: '
                    . $earliest->format('Y-m-d')
                    . '. A manager override is required for an earlier date.'
                );
            }

            $reason = trim((string) $reason);

            if (
                $isEarly &&
                mb_strlen($reason) < 10
            ) {
                $this->fail(
                    'late_addition_reason',
                    'Provide a meaningful override reason of at least 10 characters.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 5. Prevent Duplicate Active Work Orders
            |--------------------------------------------------------------------------
            */

            $existing = MaintenanceWorkOrder::query()
                ->where(
                    'complaint_id',
                    $complaint->id
                )
                ->whereIn('status', [
                    'Scheduled',
                    'In Progress',
                    'Pending Workload',
                ])
                ->exists();

            if ($existing) {
                $this->fail(
                    'complaint_id',
                    'This complaint already has an active maintenance work order.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 6. Find or Create Daily Maintenance Schedule
            |--------------------------------------------------------------------------
            */

            $schedule = MaintenanceSchedule::query()
                ->whereDate(
                    'schedule_date',
                    $dateString
                )
                ->lockForUpdate()
                ->first();

            if (!$schedule) {
                $schedule = MaintenanceSchedule::create([
                    'schedule_date' => $dateString,
                    'status' => 'Draft',
                    'created_by' => $managerId,
                ]);
            }

            /*
             * Important:
             *
             * Do not change a Finalized schedule back
             * to Draft.
             *
             * Finalized schedules remain Finalized,
             * while new work orders have their own
             * Draft approval status.
             */
            $isFinalizedSchedule = (
                $schedule->status === 'Finalized'
            );

            if (!in_array(
                $schedule->status,
                ['Draft', 'Finalized'],
                true
            )) {
                $this->fail(
                    'schedule_date',
                    'This maintenance schedule has an unsupported status.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | 7. Validate Technician Duty
            |--------------------------------------------------------------------------
            */

            $lockedDuties = $this->validation
                ->lockDuties(
                    $dateString,
                    $technicianIds
                );

            foreach ($technicianIds as $technicianId) {
                $this->validation
                    ->validateTechnicianForDate(
                        $technicianId,
                        $dateString,
                        'complaint_id',
                        $lockedDuties
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | 8. Determine Next Maintenance Visit
            |--------------------------------------------------------------------------
            */

            $visitNumber = (
                MaintenanceWorkOrder::query()
                    ->where(
                        'complaint_id',
                        $complaint->id
                    )
                    ->max('visit_number') ?? 0
            ) + 1;

            /*
            |--------------------------------------------------------------------------
            | 9. Create Draft Work Order
            |--------------------------------------------------------------------------
            */

            $workOrder = MaintenanceWorkOrder::create([
                'maintenance_schedule_id' => $schedule->id,
                'complaint_id' => $complaint->id,
                'visit_number' => $visitNumber,

                'dispatch_type' => 'Regular',

                /*
                 * Operational status remains Scheduled.
                 * Approval is tracked independently.
                 */
                'status' => 'Scheduled',

                /*
                 * Draft means not yet approved
                 * for technician execution.
                 */
                'approval_status' => 'Draft',
                'approved_by' => null,
                'approved_at' => null,

                'scheduled_by' => $managerId,

                /*
                 * Preserve the original meaning:
                 * scheduled earlier than recommended.
                 */
                'is_late_addition' => $isEarly,

                'late_addition_reason' => $isEarly
                    ? $reason
                    : null,
            ]);

            /*
            |--------------------------------------------------------------------------
            | 10. Assign Work Order Technicians
            |--------------------------------------------------------------------------
            */

            $workOrder->technicians()->sync(
                collect($technicianIds)
                    ->mapWithKeys(
                        fn ($id) => [
                            $id => [
                                'assignment_role' => 'Member',
                            ],
                        ]
                    )
                    ->all()
            );

            /*
            |--------------------------------------------------------------------------
            | 11. Record Scheduling History
            |--------------------------------------------------------------------------
            */

            $action = $isFinalizedSchedule
                ? 'Draft Addition'
                : ($isEarly
                    ? 'Manager Early Assignment'
                    : 'Scheduled');

            MaintenanceScheduleHistory::create([
                'maintenance_work_order_id' => $workOrder->id,
                'performed_by' => $managerId,
                'action' => $action,
                'old_values' => null,
                'new_values' => [
                    'schedule_date' => $dateString,
                    'status' => 'Scheduled',
                    'approval_status' => 'Draft',
                    'schedule_status' => $schedule->status,
                    'technician_ids' => $technicianIds,
                    'recommended_date' => $earliest
                        ->toDateString(),
                    'is_early' => $isEarly,
                ],
                'reason' => $isEarly
                    ? $reason
                    : null,
            ]);

            /*
             * Do not modify existing approved orders.
             * Do not change the parent schedule status.
             */
            return $workOrder->load([
                'schedule',
                'complaint',
                'technicians',
            ]);
        }, 3);
    }

    private function fail(
        string $field,
        string $message
    ): never {
        throw ValidationException::withMessages([
            $field => $message,
        ]);
    }
}
