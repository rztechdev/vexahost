<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends BaseVerifyEmail
{
    protected function buildMailMessage($url): MailMessage
    {
        return (new MailMessage)
            ->subject('Verifikasi email VexaHost')
            ->greeting('Halo,')
            ->line('Klik tombol di bawah untuk mengonfirmasi alamat email Anda.')
            ->action('Verifikasi Email', $url)
            ->line('Link berlaku ' . config('auth.verification.expire', 60) . ' menit.')
            ->line('Kalau Anda tidak mendaftar, abaikan email ini.');
    }
}
