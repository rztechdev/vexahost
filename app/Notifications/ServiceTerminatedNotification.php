<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ServiceTerminatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Subscription $subscription
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $spec = $this->subscription->vpsSpec?->name ?? 'VPS';

        return (new MailMessage)
            ->subject("Layanan dihapus permanen — {$spec}")
            ->view('emails.service-terminated', [
                'user' => $notifiable,
                'subscription' => $this->subscription,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'subscription_id' => $this->subscription->id,
            'status' => 'terminated',
        ];
    }
}
