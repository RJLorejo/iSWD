<?php

namespace App\Observers;

use App\Models\Complaint;
use App\Models\MaintenanceHistory;

class ComplaintObserver
{
    /**
     * Complaint created.
     */
    public function created(Complaint $complaint): void
    {
        MaintenanceHistory::record(
            $complaint,
            'complaint_submitted',
            'Complaint Submitted',
            'A new maintenance complaint was submitted.',
            null,
            $complaint->status,
            auth()->id()
        );
    }

    /**
     * Complaint updated.
     */
    public function updated(Complaint $complaint): void
    {
        /*
        |--------------------------------------------------------------------------
        | Status changed
        |--------------------------------------------------------------------------
        */

        if ($complaint->wasChanged('status')) {

            $oldStatus = $complaint->getOriginal('status');

            $newStatus = $complaint->status;

            $eventType = match ($newStatus) {

                'Verified' =>
                'complaint_verified',

                'Assigned' =>
                'technician_assigned',

                'In Progress' =>
                'maintenance_started',

                'Completed' =>
                'maintenance_completed',

                'Closed' =>
                'complaint_closed',

                'Rejected' =>
                'complaint_rejected',

                default =>
                'status_changed',
            };

            $title = match ($newStatus) {

                'Verified' =>
                'Complaint Verified',

                'Assigned' =>
                'Technician Assigned',

                'In Progress' =>
                'Maintenance Started',

                'Completed' =>
                'Maintenance Completed',

                'Closed' =>
                'Complaint Closed',

                'Rejected' =>
                'Complaint Rejected',

                default =>
                'Complaint Status Updated',
            };

            MaintenanceHistory::record(

                $complaint,

                $eventType,

                $title,

                "Complaint status changed from {$oldStatus} to {$newStatus}.",

                $oldStatus,

                $newStatus,

                auth()->id()
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Technician assignment changed
        |--------------------------------------------------------------------------
        */

        if ($complaint->wasChanged('assigned_to')) {

            $oldTechnician = $complaint
                ->getOriginal('assigned_to');

            $newTechnician = $complaint->assigned_to;

            MaintenanceHistory::record(

                $complaint,

                'technician_assignment_changed',

                'Technician Assignment Updated',

                $newTechnician
                    ? 'A technician was assigned to this complaint.'
                    : 'The technician assignment was removed.',

                $complaint->status,

                $complaint->status,

                auth()->id(),

                [
                    'old_technician_id' => $oldTechnician,

                    'new_technician_id' => $newTechnician,
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Verification recorded
        |--------------------------------------------------------------------------
        */

        if (
            $complaint->wasChanged('verified_at') &&
            $complaint->verified_at
        ) {

            /*
            Avoid duplicate if status update already
            generated the verification event.
            */

            $exists = MaintenanceHistory::where(
                'complaint_id',
                $complaint->id
            )
                ->where(
                    'event_type',
                    'complaint_verified'
                )
                ->exists();

            if (!$exists) {

                MaintenanceHistory::record(

                    $complaint,

                    'complaint_verified',

                    'Complaint Verified',

                    'The complaint was verified by authorized personnel.',

                    $complaint->status,

                    $complaint->status,

                    auth()->id(),

                    [
                        'verified_by' =>
                        $complaint->verified_by,

                        'verification_reason' =>
                        $complaint->verification_reason,
                    ]
                );
            }
        }
    }
}
