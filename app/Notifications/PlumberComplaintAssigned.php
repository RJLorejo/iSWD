<?php

namespace App\Notifications;

use App\Models\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PlumberComplaintAssigned extends Notification
{
    use Queueable;

    public function __construct(
        public Complaint $complaint
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->complaint->loadMissing([
            'division',
            'category',
            'aiAnalysis',
        ]);

        $divisionName =
            $this->complaint->division?->name
            ?? 'Not specified';

        $complaintType =
            $this->complaint->category?->name
            ?? 'Not specified';

        $urgency =
            $this->complaint->aiAnalysis?->urgency_level
            ?? 'Not assessed';

        $address =
            $this->complaint->address
            ?: 'Not specified';

        return (new MailMessage)
            ->subject(
                'New Maintenance Assignment - ' .
                $this->complaint->complaint_no
            )
            ->greeting(
                'Hello ' .
                ($notifiable->first_name ?? 'Plumber') .
                ','
            )
            ->line(
                'You have been assigned to a maintenance complaint by Sagay Water District.'
            )
            ->line(
                'Complaint Number: ' .
                $this->complaint->complaint_no
            )
            ->line(
                'Division: ' .
                $divisionName
            )
            ->line(
                'Complaint Type: ' .
                $complaintType
            )
            ->line(
                'Urgency: ' .
                $urgency
            )
            ->line(
                'Service Location: ' .
                $address
            )
            ->line(
                'Status: Assigned'
            )
            ->action(
                'View Assigned Complaint',
                route(
                    'technician.complaints.show',
                    $this->complaint
                )
            )
            ->line(
                'Please sign in to your iSWD account to review the complaint details and begin maintenance when appropriate.'
            )
            ->salutation(
                'Sagay Water District'
            );
    }

    public function toArray(object $notifiable): array
    {
        return [
            'complaint_id' =>
                $this->complaint->id,

            'complaint_no' =>
                $this->complaint->complaint_no,

            'status' =>
                $this->complaint->status,
        ];
    }
}
