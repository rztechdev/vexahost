<?php

namespace App\Notifications;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class InvoiceCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Invoice $invoice
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject("Tagihan Baru Diterbitkan — {$this->invoice->invoice_number}")
            ->view('emails.invoice-created', [
                'invoice' => $this->invoice,
            ]);

        try {
            $this->invoice->loadMissing(['order.customer', 'order.vpsSpec']);
            $pdf = Pdf::loadView('dashboard.invoice-pdf', ['invoice' => $this->invoice])
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'defaultFont' => 'Helvetica',
                ]);

            $mail->attachData(
                $pdf->output(),
                "Invoice-{$this->invoice->invoice_number}.pdf",
                ['mime' => 'application/pdf']
            );
        } catch (\Throwable $e) {
            Log::error('notification.invoice_pdf_attach_failed', [
                'invoice_id' => $this->invoice->id,
                'error' => $e->getMessage(),
            ]);
        }

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'amount' => (float) $this->invoice->amount,
        ];
    }
}
