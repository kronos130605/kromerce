<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'available_balance',
        'pending_balance',
        'total_earned',
        'total_withdrawn',
        'total_fees_paid',
        'currency',
        'last_payout_at',
    ];

    protected $casts = [
        'available_balance' => 'decimal:2',
        'pending_balance' => 'decimal:2',
        'total_earned' => 'decimal:2',
        'total_withdrawn' => 'decimal:2',
        'total_fees_paid' => 'decimal:2',
        'last_payout_at' => 'datetime',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function transactions()
    {
        return $this->hasMany(BalanceTransaction::class, 'store_id', 'store_id');
    }

    public function payouts()
    {
        return $this->hasMany(StorePayout::class, 'store_id', 'store_id');
    }
}
