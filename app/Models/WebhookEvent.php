<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Idempotency store untuk webhook payment gateway.
 * Unique (provider, event_id) memastikan webhook duplikat tidak diproses 2x.
 *
 * processing_status: received | processed | failed | ignored
 */
class WebhookEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'event_id',
        'event_type',
        'signature',
        'ip_address',
        'payload',
        'processing_status',
        'processing_error',
        'processed_at',
        'order_id',
        'payment_transaction_id',
    ];

    protected $casts = [
        'payload' => 'array',
        'processed_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function paymentTransaction()
    {
        return $this->belongsTo(PaymentTransaction::class);
    }
}
