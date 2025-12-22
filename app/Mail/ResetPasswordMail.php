<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $resetUrl;

    public function __construct(string $token, string $email)
    {
        $this->resetUrl = url("/password/reset/{$token}");
    }

    public function build()
    {
        return $this->subject('Reset Your Password - EatZy')
            ->view('emails.reset-password');
    }
}
