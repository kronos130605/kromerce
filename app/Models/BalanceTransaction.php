<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BalanceTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'type',
        'amount',
        'currency',
        'balance_after',
        'order_id',
        'payout_id',
        'fee_id',
        'description',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function payout()
    {
        return $this->belongsTo(StorePayout::class, 'payout_id');
    }

    public function fee()
    {
        return $this->belongsTo(PlatformFee::class, 'fee_id');
    }
}
