<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConsumerAccountVerified extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your iSWD Consumer Account Has Been Verified')
            ->greeting('Good day, ' . $notifiable->first_name . '!')
            ->line(
                'Your consumer account registration with Sagay Water District has been successfully verified.'
            )
            ->line(
                'Your iSWD Consumer Portal account is now active, and you may sign in using the email address and password you registered.'
            )
            ->action(
                'Log In to iSWD',
                route('login')
            )
            ->line(
                'For your security, never share your password with anyone.'
            )
            ->line(
                'Thank you for using the iSWD Consumer Portal.'
            );
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
