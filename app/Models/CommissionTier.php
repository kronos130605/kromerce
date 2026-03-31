<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category_id',
        'percentage',
        'fixed_amount',
        'min_order_value',
        'max_order_value',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'fixed_amount' => 'decimal:2',
        'min_order_value' => 'decimal:2',
        'max_order_value' => 'decimal:2',
        'priority' => 'integer',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function storeConfigs()
    {
        return $this->hasMany(StoreCommissionConfig::class, 'tier_id');
    }

    public function platformFees()
    {
        return $this->hasMany(PlatformFee::class, 'tier_id');
    }
}
