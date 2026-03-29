<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatformFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'store_id',
        'tier_id',
        'order_subtotal',
        'commission_percentage',
        'commission_amount',
        'fixed_fee',
        'total_fee',
        'currency',
        'status',
        'collected_at',
        'refunded_at',
        'calculation_details',
    ];

    protected $casts = [
        'order_subtotal' => 'decimal:2',
        'commission_percentage' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'fixed_fee' => 'decimal:2',
        'total_fee' => 'decimal:2',
        'collected_at' => 'datetime',
        'refunded_at' => 'datetime',
        'calculation_details' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function tier()
    {
        return $this->belongsTo(CommissionTier::class, 'tier_id');
    }
}
