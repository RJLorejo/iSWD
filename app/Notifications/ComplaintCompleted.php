<?php

namespace App\Notifications;

use App\Models\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ComplaintCompleted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Complaint $complaint
    ) {
        $this->afterCommit();
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
                ' - Officially Closed'
            )
            ->greeting(
                'Hello ' .
                ($notifiable->first_name ?? 'Consumer') .
                ','
            )
            ->line(
                'Your complaint submitted to Sagay Water District has been officially resolved and closed by Customer Service.'
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
                'Final Status: Closed'
            );

        if ($isCommercial) {
            $mail->line(
                'Customer Service has finalized the resolution of your commercial service concern.'
            );
        } else {
            $mail->line(
                'The maintenance work has been accomplished, the accomplishment report has been reviewed, and Customer Service has finalized your complaint.'
            );
        }

        return $mail
            ->line(
                'You may review the complaint details and its final status through your iSWD Consumer Portal.'
            )
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
            'complaint_id' => $this->complaint->id,
            'complaint_no' => $this->complaint->complaint_no,
            'status' => $this->complaint->status,
        ];
    }
}
