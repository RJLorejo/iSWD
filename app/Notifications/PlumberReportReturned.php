<?php

namespace App\Notifications;

use App\Models\MaintenanceReport;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlumberReportReturned extends Notification
{
    use Queueable;

    public function __construct(
        public MaintenanceReport $maintenanceReport
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->maintenanceReport->loadMissing([
            'complaint.category',
            'complaint.division',
        ]);

        $complaint = $this->maintenanceReport->complaint;

        $complaintType =
            $complaint?->category?->name
            ?? 'Not specified';

        $division =
            $complaint?->division?->name
            ?? 'Not specified';

        $remarks =
            $this->maintenanceReport->review_remarks
            ?? 'Please review and correct the accomplishment report.';

        return (new MailMessage)
            ->subject(
                'Accomplishment Report Returned - ' .
                ($complaint?->complaint_no ?? 'Complaint')
            )
            ->greeting(
                'Hello ' .
                ($notifiable->first_name ?? 'Plumber') .
                ','
            )
            ->line(
                'The Maintenance Manager returned the accomplishment report for correction.'
            )
            ->line(
                'Complaint Number: ' .
                ($complaint?->complaint_no ?? 'Not specified')
            )
            ->line(
                'Division: ' .
                $division
            )
            ->line(
                'Complaint Type: ' .
                $complaintType
            )
            ->line(
                'Report Status: Returned'
            )
            ->line(
                'Manager\'s Remarks:'
            )
            ->line(
                $remarks
            )
            ->action(
                'Revise Accomplishment Report',
                route(
                    'technician.maintenance-reports.create',
                    $complaint
                )
            )
            ->line(
                'Please review the required corrections and update the shared accomplishment report.'
            )
            ->salutation(
                'Sagay Water District'
            );
    }

    public function toArray(object $notifiable): array
    {
        return [
            'maintenance_report_id' =>
                $this->maintenanceReport->id,

            'complaint_id' =>
                $this->maintenanceReport->complaint_id,

            'review_status' =>
                $this->maintenanceReport->review_status,
        ];
    }
}
