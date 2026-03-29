<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisputeResolution extends Model
{
    use HasFactory;

    protected $fillable = [
        'dispute_id',
        'resolution_type',
        'amount',
        'description',
        'resolved_by',
        'resolved_at',
        'customer_accepted',
        'seller_accepted',
        'customer_accepted_at',
        'seller_accepted_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'resolved_at' => 'datetime',
        'customer_accepted' => 'boolean',
        'seller_accepted' => 'boolean',
        'customer_accepted_at' => 'datetime',
        'seller_accepted_at' => 'datetime',
    ];

    public function dispute()
    {
        return $this->belongsTo(OrderDispute::class, 'dispute_id');
    }

    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
