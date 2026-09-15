<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketRepliedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public SupportTicket $ticket,
        public string $replyMessage
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("[Tiket #{$this->ticket->id}] Balasan dari Tim Support: {$this->ticket->subject}")
            ->view('emails.ticket-replied', [
                'user' => $notifiable,
                'ticket' => $this->ticket,
                'replyMessage' => $this->replyMessage,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'subject' => $this->ticket->subject,
            'message' => mb_strimwidth($this->replyMessage, 0, 100, '...'),
        ];
    }
}
