<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StaffPasswordResetOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $otp
    ) {}

    public function build()
    {
        return $this->subject('iSWD Employee Password Reset Code')
            ->view('emails.staff-password-reset-otp');
    }
}
