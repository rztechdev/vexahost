<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeCredentialsNotification extends Notification
{
    use Queueable;

    public function __construct(
        public ?string $plainPassword = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Selamat Datang di VexaHost Cloud — Informasi Akses Akun")
            ->view('emails.welcome-credentials', [
                'user' => $notifiable,
                'plainPassword' => $this->plainPassword,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $notifiable->id,
            'username' => $notifiable->username,
            'email' => $notifiable->email,
        ];
    }
}
