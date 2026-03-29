<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StorePayout extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'payout_number',
        'amount',
        'currency',
        'status',
        'method',
        'payment_details',
        'requested_by',
        'requested_at',
        'processed_by',
        'processed_at',
        'completed_at',
        'transaction_id',
        'notes',
        'failure_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_details' => 'array',
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function transactions()
    {
        return $this->hasMany(BalanceTransaction::class, 'payout_id');
    }
}
