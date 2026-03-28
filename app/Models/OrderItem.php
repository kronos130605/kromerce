<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'uuid',
        'product_name',
        'product_sku',
        'product_type',
        'product_description',
        'product_image',
        'variant_attributes',
        'currency',
        'unit_price',
        'unit_cost',
        'quantity',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total',
        'unit_weight',
        'total_weight',
        'fulfillment_status',
        'quantity_fulfilled',
        'quantity_returned',
        'fulfilled_at',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'unit_price' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'unit_weight' => 'decimal:2',
        'total_weight' => 'decimal:2',
        'variant_attributes' => 'array',
        'metadata' => 'array',
        'fulfilled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the order that owns the item.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product that owns the item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the product variant that owns the item.
     */
    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    /**
     * Get the formatted unit price.
     */
    public function getFormattedUnitPriceAttribute(): string
    {
        return number_format($this->unit_price, 2);
    }

    /**
     * Get the formatted total price.
     */
    public function getFormattedTotalPriceAttribute(): string
    {
        return number_format($this->total, 2);
    }

    /**
     * Get the product name from snapshot if product is deleted.
     */
    public function getProductNameAttribute(): string
    {
        if ($this->product && !$this->product->trashed()) {
            return $this->product->name;
        }
        
        return $this->attributes['product_name'] ?? 'Deleted Product';
    }

    /**
     * Get the product image from snapshot or product.
     */
    public function getProductImageAttribute(): ?string
    {
        if ($this->product && !$this->product->trashed()) {
            return $this->product->image_url ?? null;
        }
        
        return $this->attributes['product_image'] ?? null;
    }
}
