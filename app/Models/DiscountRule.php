<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiscountRule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'store_id',
        'name',
        'description',
        'priority',
        'type',
        'discount_value',
        'currency',
        'minimum_purchase_amount',
        'minimum_items_quantity',
        'applicable_products',
        'applicable_categories',
        'excluded_products',
        'excluded_categories',
        'allowed_customer_groups',
        'first_order_only',
        'apply_to_sale_items',
        'starts_at',
        'expires_at',
        'status',
        'stop_further_rules',
        'created_by',
    ];

    protected $casts = [
        'priority' => 'integer',
        'discount_value' => 'decimal:2',
        'minimum_purchase_amount' => 'decimal:2',
        'minimum_items_quantity' => 'integer',
        'applicable_products' => 'array',
        'applicable_categories' => 'array',
        'excluded_products' => 'array',
        'excluded_categories' => 'array',
        'allowed_customer_groups' => 'array',
        'first_order_only' => 'boolean',
        'apply_to_sale_items' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'stop_further_rules' => 'boolean',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
