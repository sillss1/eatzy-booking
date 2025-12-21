<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class MailModel extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS', 'noreply@eatzy.com'), env('MAIL_FROM_NAME', 'EatZy')),
            subject: $this->mailData['subject'] ?? 'EatZy Notification',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: $this->mailData['view'] ?? 'emails.reset-password',
        );
    }
}
