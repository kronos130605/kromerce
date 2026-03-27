<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ProductVariant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'sku',
        'barcode',
        'base_price',
        'base_sale_price',
        'cost_price',
        'manage_stock',
        'stock_quantity',
        'stock_status',
        'low_stock_threshold',
        'weight',
        'length',
        'width',
        'height',
        'status',
        'enabled',
        'attributes',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'base_sale_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'manage_stock' => 'boolean',
        'stock_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
        'weight' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'enabled' => 'boolean',
        'attributes' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($variant) {
            if (empty($variant->sku)) {
                $variant->sku = $variant->generateSku();
            }
        });
    }

    /**
     * Get the product that owns the variant.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the images for this variant.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductVariantImage::class)->orderBy('order');
    }

    /**
     * Get the current effective price.
     */
    public function getCurrentPrice(): float
    {
        if ($this->base_sale_price && $this->product->isCurrentlyOnSale()) {
            return $this->base_sale_price;
        }
        return $this->base_price ?: $this->product->base_price;
    }

    /**
     * Check if variant is in stock.
     */
    public function isInStock(): bool
    {
        if (!$this->manage_stock) {
            return true;
        }
        
        return $this->stock_quantity > 0;
    }

    /**
     * Check if variant has low stock.
     */
    public function hasLowStock(): bool
    {
        if (!$this->manage_stock) {
            return false;
        }
        
        return $this->stock_quantity <= $this->low_stock_threshold;
    }

    /**
     * Generate SKU for variant.
     */
    private function generateSku(): string
    {
        $storeSlug = $this->product->store->slug ?? 'STORE';
        $productSlug = Str::upper(Str::substr(Str::slug($this->product->name), 0, 6));
        
        // Add attribute identifiers
        $attrPart = '';
        if ($this->attributes) {
            $attrValues = collect($this->attributes)
                ->map(fn($value) => Str::upper(Str::substr($value, 0, 3)))
                ->implode('-');
            $attrPart = '-' . $attrPart;
        }
        
        $random = strtoupper(Str::random(3));
        
        return "{$storeSlug}-{$productSlug}{$attrPart}-{$random}";
    }

    /**
     * Get formatted attributes string.
     */
    public function getAttributesString(): string
    {
        if (!$this->attributes) {
            return '';
        }

        return collect($this->attributes)
            ->map(fn($value, $key) => "{$key}: {$value}")
            ->implode(', ');
    }

    /**
     * Get variant display name.
     */
    public function getDisplayName(): string
    {
        $attributesString = $this->getAttributesString();
        
        return $attributesString 
            ? "{$this->product->name} - {$attributesString}"
            : $this->product->name;
    }

    /**
     * Scope to get only enabled variants.
     */
    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }

    /**
     * Scope to get only active variants.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')->enabled();
    }

    /**
     * Scope to get only in-stock variants.
     */
    public function scopeInStock($query)
    {
        return $query->where(function ($q) {
            $q->where('manage_stock', false)
              ->orWhere('stock_quantity', '>', 0);
        });
    }
}
