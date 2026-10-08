<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ConsumerEmailOtpMail extends Mailable
{
    use Queueable;
    use SerializesModels;


    public function __construct(
        public string $otp
    ) {
    }


    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'iSWD Email Verification Code'
        );
    }


    public function content(): Content
    {
        return new Content(
            view: 'emails.consumer-email-otp'
        );
    }


    public function attachments(): array
    {
        return [];
    }
}
