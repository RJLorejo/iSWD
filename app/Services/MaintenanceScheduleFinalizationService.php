<?php

namespace App\Services;

use App\Models\MaintenanceSchedule;
use Illuminate\Validation\ValidationException;

class MaintenanceScheduleFinalizationService
{
    public function __construct(
        private MaintenanceWorkOrderValidationService $validation
    ) {}

    /**
     * Validate a draft daily workload before manager finalization.
     *
     * Daily workload planning does not require start/end times.
     * One plumber may be assigned to multiple complaints.
     *
     * Call inside a transaction after locking the schedule.
     */
    public function validate(MaintenanceSchedule $schedule): void
    {
        if ($schedule->status !== 'Draft') {
            $this->fail(
                'Only a Draft maintenance schedule can be finalized.'
            );
        }

        $date = $schedule->schedule_date->format('Y-m-d');

        $this->validation->validateWorkingDate(
            $date,
            'schedule'
        );

        $orders = $schedule->workOrders()
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        if ($orders->isEmpty()) {
            $this->fail(
                'Add at least one maintenance work order before finalizing.'
            );
        }

        /*
         * Only Scheduled and Cancelled records should exist
         * in a new draft workload.
         *
         * Pending Workload from a previous day must be reviewed
         * and rescheduled as Scheduled before finalization.
         */
        $unresolved = $orders->first(function ($order) {
            return !in_array(
                $order->status,
                ['Scheduled', 'Cancelled'],
                true
            );
        });

        if ($unresolved) {
            $this->fail(
                "Work order #{$unresolved->id} has status "
                    . "{$unresolved->status}. Resolve it before finalizing."
            );
        }

        $scheduledOrders = $orders->where('status', 'Scheduled');

        if ($scheduledOrders->isEmpty()) {
            $this->fail(
                'Add at least one Scheduled work order before finalizing.'
            );
        }

        $teams = [];
        $allTechnicianIds = [];

        /*
         * Collect assigned plumbers.
         *
         * Repeated plumber IDs across different complaints
         * are allowed in daily workload planning.
         */
        foreach ($scheduledOrders as $order) {
            $teamIds = $this->validation->teamIds($order);

            if (empty($teamIds)) {
                $this->fail(
                    "Work order #{$order->id} has no assigned plumbers."
                );
            }

            $teams[$order->id] = $teamIds;

            foreach ($teamIds as $technicianId) {
                $allTechnicianIds[] = (int) $technicianId;
            }
        }

        $allTechnicianIds = array_values(
            array_unique($allTechnicianIds)
        );

        sort($allTechnicianIds);

        /*
         * Lock technician duty records.
         */
        $duties = $this->validation->lockDuties(
            $date,
            $allTechnicianIds
        );

        /*
         * Validate active role and duty availability.
         *
         * No planned time validation.
         * No overlapping workload validation.
         */
        foreach ($allTechnicianIds as $technicianId) {
            $this->validation->validateTechnicianForDate(
                $technicianId,
                $date,
                'schedule',
                $duties
            );
        }

        /*
         * Prevent the same complaint from appearing twice
         * as an active work order in this daily schedule.
         */
        $complaintIds = [];

        foreach ($scheduledOrders as $order) {
            if (in_array((int) $order->complaint_id, $complaintIds, true)) {
                $this->fail(
                    "Complaint #{$order->complaint_id} appears more than once in this daily workload."
                );
            }

            $complaintIds[] = (int) $order->complaint_id;
        }
    }

    private function fail(string $message): never
    {
        throw ValidationException::withMessages([
            'schedule' => $message,
        ]);
    }
}
