<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ServiceSuspendedNotification extends Notification
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
            ->subject("Layanan ditangguhkan (overdue) — {$spec}")
            ->view('emails.service-suspended', [
                'user' => $notifiable,
                'subscription' => $this->subscription,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'subscription_id' => $this->subscription->id,
            'status' => 'suspended',
            'amount' => (float) $this->subscription->unit_amount,
        ];
    }
}
