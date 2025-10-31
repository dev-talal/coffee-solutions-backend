<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $password;
    public $isCustomer;

    public function __construct($email, $password, $isCustomer)
    {
        $this->email = $email;
        $this->password = $password;
        $this->isCustomer = $isCustomer;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Our ' . env('APP_NAME'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.welcome',
            with: [
                'email' => $this->email,
                'password' => $this->password,
                'isCustomer' => $this->isCustomer,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
