<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'organization_id',
        'assigned_to',
        'vps_instance_id',
        'subject',
        'status',
        'priority',
        'sla_due_at',
        'first_response_at',
        'last_customer_reply_at',
        'resolved_at',
    ];

    protected $casts = [
        'sla_due_at' => 'datetime',
        'first_response_at' => 'datetime',
        'last_customer_reply_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function vpsInstance()
    {
        return $this->belongsTo(VpsInstance::class, 'vps_instance_id');
    }

    public function organization() { return $this->belongsTo(Organization::class); }
    public function assignee() { return $this->belongsTo(User::class, 'assigned_to'); }

    public function messages()
    {
        return $this->hasMany(TicketMessage::class, 'ticket_id')->orderBy('created_at', 'asc');
    }

    public function customerMessages()
    {
        return $this->hasMany(TicketMessage::class, 'ticket_id')->where('is_internal', false)->orderBy('created_at', 'asc');
    }

    public function latestMessage()
    {
        return $this->hasOne(TicketMessage::class, 'ticket_id')->latestOfMany();
    }

    public function latestCustomerMessage()
    {
        return $this->hasOne(TicketMessage::class, 'ticket_id')->where('is_internal', false)->latestOfMany();
    }
}
