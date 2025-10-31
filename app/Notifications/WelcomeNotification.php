<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification
{
    use Queueable;

    protected $email;
    protected $plainPassword;

    public function __construct($email, $plainPassword)
    {
        $this->email = $email;
        $this->plainPassword = $plainPassword;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Welcome to Our ' . env('APP_NAME'))
            ->greeting('Welcome!')
            ->line('Your account has been created successfully.')
            ->line('Here are your login credentials:')
            ->line('**Email:** ' . $this->email)
            ->line('**Password:** ' . $this->plainPassword);

        if ($notifiable->hasRole('customer')) {
            $mail->action('Download on Play Store', 'https://play.google.com/store/apps/details?id=your.app.package');
            $mail->action('Download on App Store', 'https://apps.apple.com/app/idYOUR_APP_ID');
        } else {
            $mail->action('Access Your Account', env('FRONTEND_URL'));
        }

        $mail->line('Please log in and change your password after your first login.');

        return $mail;
    }
}
