<?php

namespace App\Notifications;

use App\Models\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ComplaintCompleted extends Notification
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
        ]);

        $divisionName = trim(
            (string) $this->complaint->division?->name
        );

        $isCommercial = str_contains(
            strtolower($divisionName),
            'commercial'
        );

        $complaintType =
            $this->complaint->category?->name
            ?? 'Not specified';

        $mail = (new MailMessage)
            ->subject(
                'Complaint ' .
                $this->complaint->complaint_no .
                ' Has Been Completed'
            )
            ->greeting(
                'Hello ' .
                ($notifiable->first_name ?? 'Consumer') .
                ','
            )
            ->line(
                'Your complaint with Sagay Water District has been completed.'
            )
            ->line(
                'Complaint Number: ' .
                $this->complaint->complaint_no
            )
            ->line(
                'Division: ' .
                ($divisionName ?: 'Not specified')
            )
            ->line(
                'Complaint Type: ' .
                $complaintType
            )
            ->line(
                'Status: Completed'
            );

        if ($isCommercial) {
            $mail->line(
                'The findings and resolution from Customer Service are now available in your iSWD account.'
            );
        } else {
            $mail->line(
                'The completed service details for your Engineering Operation complaint are now available in your iSWD account.'
            );
        }

        return $mail
            ->action(
                'View My Complaint',
                route(
                    'consumer.complaints.show',
                    $this->complaint
                )
            )
            ->line(
                'Please sign in to your iSWD account to view the complaint details.'
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
