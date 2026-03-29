<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDispute extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'dispute_number',
        'order_id',
        'store_id',
        'customer_id',
        'type',
        'subject',
        'description',
        'evidence',
        'status',
        'priority',
        'resolution_type',
        'refund_amount',
        'resolution_notes',
        'resolved_at',
        'assigned_to',
        'assigned_at',
        'is_escalated',
        'escalated_at',
        'escalation_reason',
    ];

    protected $casts = [
        'evidence' => 'array',
        'refund_amount' => 'decimal:2',
        'resolved_at' => 'datetime',
        'assigned_at' => 'datetime',
        'is_escalated' => 'boolean',
        'escalated_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function messages()
    {
        return $this->hasMany(DisputeMessage::class, 'dispute_id');
    }

    public function resolutions()
    {
        return $this->hasMany(DisputeResolution::class, 'dispute_id');
    }
}
