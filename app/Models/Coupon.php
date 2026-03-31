<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'store_id',
        'code',
        'name',
        'description',
        'type',
        'discount_value',
        'currency',
        'usage_limit',
        'usage_limit_per_user',
        'usage_count',
        'minimum_purchase_amount',
        'maximum_discount_amount',
        'starts_at',
        'expires_at',
        'applicable_products',
        'applicable_categories',
        'excluded_products',
        'excluded_categories',
        'apply_to_sale_items',
        'buy_quantity',
        'get_quantity',
        'get_discount_percentage',
        'allowed_user_ids',
        'allowed_customer_groups',
        'first_order_only',
        'status',
        'is_public',
        'combinable_with_other_coupons',
        'created_by',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'usage_limit' => 'integer',
        'usage_limit_per_user' => 'integer',
        'usage_count' => 'integer',
        'minimum_purchase_amount' => 'decimal:2',
        'maximum_discount_amount' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'applicable_products' => 'array',
        'applicable_categories' => 'array',
        'excluded_products' => 'array',
        'excluded_categories' => 'array',
        'apply_to_sale_items' => 'boolean',
        'buy_quantity' => 'integer',
        'get_quantity' => 'integer',
        'get_discount_percentage' => 'decimal:2',
        'allowed_user_ids' => 'array',
        'allowed_customer_groups' => 'array',
        'first_order_only' => 'boolean',
        'is_public' => 'boolean',
        'combinable_with_other_coupons' => 'boolean',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }
}
