<?php

namespace App\Notifications;

use App\Models\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class ComplaintAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Complaint $complaint
    ) {}

    public function via(object $notifiable): array
    {
        return [
            'database',
            'broadcast',
        ];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'complaint_id' => $this->complaint->id,
            'complaint_no' => $this->complaint->complaint_no,
            'title' => 'Complaint Assigned',
            'message' => 'Your complaint has been assigned to the maintenance team.',
            'status' => $this->complaint->status,
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'complaint_id' => $this->complaint->id,
            'complaint_no' => $this->complaint->complaint_no,
            'title' => 'Complaint Assigned',
            'message' => 'Your complaint has been assigned to the maintenance team.',
            'status' => $this->complaint->status,
        ]);
    }
}
