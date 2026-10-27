<?php

namespace App\Services;

use App\Models\Complaint;
use App\Models\MaintenanceWorkOrder;
use Carbon\CarbonImmutable;

class TechnicianWorkOrderAccessService
{
    private const TIMEZONE = 'Asia/Manila';

    public function findForTechnician(Complaint $complaint, int $technicianId): ?MaintenanceWorkOrder
    {
        return MaintenanceWorkOrder::query()
            ->with('schedule')
            ->where('complaint_id', $complaint->id)
            ->whereHas('technicians', fn ($query) => $query->where('users.id', $technicianId))
            ->whereNotIn('status', ['Cancelled', 'Rescheduled'])
            ->orderByDesc('visit_number')
            ->orderByDesc('id')
            ->first();
    }

    /** Display-only state; POST controllers must independently authorize actions. */
    public function status(?MaintenanceWorkOrder $workOrder, ?Complaint $complaint = null): array
    {
        $today = CarbonImmutable::now(self::TIMEZONE)->toDateString();
        $base = [
            'state' => 'unscheduled',
            'label' => 'Not Scheduled',
            'can_start' => false,
            'message' => 'No active maintenance work order is available.',
            'schedule_date' => null,
            'start_time' => null,
            'end_time' => null,
            'approval_status' => null,
        ];

        if (!$workOrder) {
            if ($complaint?->status === 'In Progress') {
                return array_merge($base, [
                    'state' => 'legacy_review',
                    'label' => 'Manager Review Required',
                    'message' => 'This complaint is In Progress but has no maintenance work order. Its previous maintenance activity must be reviewed before rescheduling.',
                ]);
            }
            return $base;
        }

        $schedule = $workOrder->schedule;
        $date = $schedule?->schedule_date
            ? CarbonImmutable::parse($schedule->schedule_date, self::TIMEZONE)->toDateString()
            : null;
        $approved = $workOrder->approval_status === 'Approved';
        $scheduleFinalized = $schedule?->status === 'Finalized';

        $result = array_merge($base, [
            'state' => 'pending',
            'label' => 'Pending Approval',
            'message' => 'Waiting for Maintenance Manager approval of this work order.',
            'schedule_date' => $date,
            'approval_status' => $workOrder->approval_status,
        ]);

        // Completed or previously started visits retain their operational history.
        if ($workOrder->status === 'Completed') {
            return array_merge($result, [
                'state' => 'completed',
                'label' => 'Completed',
                'message' => 'Maintenance work has been completed.',
            ]);
        }

        if ($workOrder->status === 'Pending Workload') {
            return array_merge($result, [
                'state' => 'pending_workload',
                'label' => 'Pending Workload',
                'message' => 'Maintenance was interrupted. You may resume on the same approved scheduled day; otherwise the manager will plan a follow-up visit.',
            ]);
        }

        if ($workOrder->status === 'In Progress') {
            if (!$approved || !$scheduleFinalized) {
                return array_merge($result, [
                    'state' => 'approval_review',
                    'label' => 'Manager Review Required',
                    'message' => 'This in-progress work order has an approval inconsistency. Contact the Maintenance Manager before continuing.',
                ]);
            }
            $startedDate = $workOrder->started_at
                ? CarbonImmutable::parse($workOrder->started_at)->setTimezone(self::TIMEZONE)->toDateString()
                : null;
            if ($startedDate !== null && $startedDate < $today) {
                return array_merge($result, [
                    'state' => 'unfinished_previous_day',
                    'label' => 'Unfinished Maintenance',
                    'message' => 'This maintenance started on a previous day. Record the Pending Workload reason before requesting another schedule.',
                ]);
            }
            return array_merge($result, [
                'state' => 'in_progress',
                'label' => 'In Progress',
                'message' => 'Maintenance is currently in progress.',
            ]);
        }

        if ($workOrder->status !== 'Scheduled') {
            return array_merge($result, [
                'state' => 'unavailable',
                'label' => $workOrder->status,
                'message' => 'This work order is not available for technician execution.',
            ]);
        }

        if (!$schedule || !$date) {
            return array_merge($result, [
                'state' => 'schedule_missing',
                'label' => 'Schedule Missing',
                'message' => 'The maintenance schedule or date needs manager review.',
            ]);
        }

        // Parent finalization alone does not approve newly added work orders.
        if (!$approved || !$scheduleFinalized) {
            return array_merge($result, [
                'state' => 'pending',
                'label' => 'Pending Approval',
                'message' => !$approved
                    ? 'This work order is saved as Draft. The Maintenance Manager must approve this assignment before maintenance can begin.'
                    : 'This work order is approved, but its daily schedule has not been finalized.',
            ]);
        }

        if ($date > $today) {
            return array_merge($result, [
                'state' => 'upcoming',
                'label' => 'Upcoming',
                'message' => 'This work order is approved. Maintenance can start on the scheduled date.',
            ]);
        }

        if ($date < $today) {
            return array_merge($result, [
                'state' => 'overdue',
                'label' => 'Rescheduling Required',
                'message' => 'The approved scheduled date has passed. Contact the Maintenance Manager.',
            ]);
        }

        return array_merge($result, [
            'state' => 'ready',
            'label' => 'Ready to Start',
            'can_start' => true,
            'message' => 'This work order is approved and the schedule is finalized. Maintenance is authorized for today.',
        ]);
    }
}
