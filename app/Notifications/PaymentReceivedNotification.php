<?php

namespace App\Notifications;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceivedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Order $order,
        public ?Invoice $invoice = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $invNo = $this->invoice?->invoice_number ?? ('INV-' . $this->order->id);

        return (new MailMessage)
            ->subject("Pembayaran diterima — {$invNo}")
            ->view('emails.payment-received', [
                'order' => $this->order,
                'invoice' => $this->invoice,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'invoice_id' => $this->invoice?->id,
            'amount' => (float) $this->order->amount,
        ];
    }
}
