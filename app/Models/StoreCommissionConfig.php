<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreCommissionConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'tier_id',
        'custom_percentage',
        'custom_fixed_amount',
        'effective_from',
        'effective_until',
        'notes',
    ];

    protected $casts = [
        'custom_percentage' => 'decimal:2',
        'custom_fixed_amount' => 'decimal:2',
        'effective_from' => 'date',
        'effective_until' => 'date',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function tier()
    {
        return $this->belongsTo(CommissionTier::class, 'tier_id');
    }
}
