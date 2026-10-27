<?php

namespace App\Notifications;

use App\Models\MaintenanceWorkOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MaintenanceScheduleFinalized extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public MaintenanceWorkOrder $workOrder,
        public string $notificationType = 'finalized'
    ) {
        $this->afterCommit();
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->workOrder->loadMissing([
            'complaint.division',
            'complaint.category',
            'complaint.aiAnalysis',
            'schedule',
        ]);

        $complaint = $this->workOrder->complaint;

        $complaintNo = $complaint?->complaint_no
            ?? 'Not specified';

        $division = $complaint?->division?->name
            ?? 'Not specified';

        $category = $complaint?->category?->name
            ?? 'Not specified';

        $urgency = $complaint?->aiAnalysis?->urgency_level
            ?? 'Not assessed';

        $address = $complaint?->address
            ?: 'Not specified';

        $scheduleDate = $this->workOrder->schedule?->schedule_date
            ? \Illuminate\Support\Carbon::parse(
                $this->workOrder->schedule->schedule_date
            )->format('F j, Y')
            : 'Not specified';

        $subject = match ($this->notificationType) {
            'addition' => 'New Work Order Added to Finalized Schedule - ' . $complaintNo,
            'replacement' => 'Maintenance Assignment Updated - ' . $complaintNo,
            default => 'Maintenance Schedule Finalized - ' . $complaintNo,
        };

        $intro = match ($this->notificationType) {
            'addition' => 'A new maintenance work order has been approved and added to the finalized schedule.',
            'replacement' => 'Your maintenance work-order assignment has been updated by the Maintenance Manager.',
            default => 'The Maintenance Manager has finalized the schedule for your maintenance work order.',
        };

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Hello ' . ($notifiable->first_name ?? 'Technician') . ',')
            ->line($intro)
            ->line('Work Order: #' . $this->workOrder->id)
            ->line('Complaint Number: ' . $complaintNo)
            ->line('Scheduled Date: ' . $scheduleDate)
            ->line('Division: ' . $division)
            ->line('Complaint Type: ' . $category)
            ->line('Urgency: ' . $urgency)
            ->line('Service Location: ' . $address)
            ->line('Work Order Status: ' . ($this->workOrder->status ?? 'Scheduled'))
            ->action(
                'View Assigned Complaint',
                route('technician.complaints.show', $complaint)
            )
            ->line('Please sign in to your iSWD account to review the assignment and scheduled maintenance details.')
            ->salutation('Sagay Water District');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'maintenance_work_order_id' => $this->workOrder->id,
            'complaint_id' => $this->workOrder->complaint_id,
            'complaint_no' => $this->workOrder->complaint?->complaint_no,
            'notification_type' => $this->notificationType,
            'status' => $this->workOrder->status,
        ];
    }
}
