<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\Store;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'store_id',
        'name',
        'slug',
        'description',
        'short_description',
        'base_currency',
        'base_price',
        'base_sale_price',
        'cost_price',
        'is_on_sale',
        'sale_type',
        'sale_discount',
        'sale_start_date',
        'sale_end_date',
        'historical_cost_currency',
        'historical_cost_amount',
        'historical_cost_rate',
        'historical_cost_date',
        'track_cost',
        'show_cost_to_customer',
        'sku',
        'barcode',
        'status',
        'visibility',
        'featured',
        'downloadable',
        'virtual',
        'product_type',
        'manage_stock',
        'stock_quantity',
        'stock_status',
        'low_stock_threshold',
        'weight',
        'length',
        'width',
        'height',
        'shipping_class',
        'free_shipping',
        'tax_class',
        'tax_status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'base_sale_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'is_on_sale' => 'boolean',
        'sale_discount' => 'decimal:2',
        'historical_cost_amount' => 'decimal:2',
        'historical_cost_rate' => 'decimal:6',
        'historical_cost_date' => 'date',
        'track_cost' => 'boolean',
        'show_cost_to_customer' => 'boolean',
        'featured' => 'boolean',
        'downloadable' => 'boolean',
        'virtual' => 'boolean',
        'manage_stock' => 'boolean',
        'stock_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
        'weight' => 'decimal:2',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'free_shipping' => 'boolean',
        'sale_start_date' => 'date',
        'sale_end_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }

            // Generate SKU if not provided
            if (empty($product->sku)) {
                $product->sku = $product->generateSku();
            }

            // Set historical cost data if cost_price is provided
            if ($product->cost_price && !$product->historical_cost_amount) {
                $product->setHistoricalCostData();
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && !$product->isDirty('slug')) {
                $product->slug = Str::slug($product->name);
            }

            // Track price changes
            if ($product->isDirty(['base_price', 'base_sale_price'])) {
                $product->recordPriceChange();
            }
        });
    }

    /**
     * Get the store that owns the product.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the user who created the product.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the product.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the categories that belong to the product.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ProductCategory::class, 'product_category_product')
            ->withPivot('order')
            ->withTimestamps()
            ->orderBy('pivot_order');
    }

    /**
     * Get the tags that belong to the product.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(ProductTag::class, 'product_product_tag')
            ->withTimestamps();
    }

    /**
     * Get the images for the product.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }

    /**
     * Get the primary image for the product.
     */
    public function primaryImage(): HasMany
    {
        return $this->hasMany(ProductImage::class)->where('is_primary', true);
    }

    /**
     * Get the variants for the product.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Get the price history for the product.
     */
    public function priceHistory(): HasMany
    {
        return $this->hasMany(ProductPriceHistory::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get current effective price (sale price if on sale, otherwise base price).
     */
    public function getCurrentPrice(): float
    {
        if ($this->is_on_sale && $this->base_sale_price) {
            return $this->base_sale_price;
        }
        return $this->base_price;
    }

    /**
     * Check if product is currently on sale.
     */
    public function isCurrentlyOnSale(): bool
    {
        if (!$this->is_on_sale) {
            return false;
        }

        $now = now();

        if ($this->sale_start_date && $now->lt($this->sale_start_date)) {
            return false;
        }

        if ($this->sale_end_date && $now->gt($this->sale_end_date)) {
            return false;
        }

        return true;
    }

    /**
     * Check if product is in stock.
     */
    public function isInStock(): bool
    {
        if (!$this->manage_stock) {
            return true;
        }

        return $this->stock_quantity > 0;
    }

    /**
     * Check if product has low stock.
     */
    public function hasLowStock(): bool
    {
        if (!$this->manage_stock) {
            return false;
        }

        return $this->stock_quantity <= $this->low_stock_threshold;
    }

    /**
     * Generate SKU based on store and product name.
     */
    private function generateSku(): string
    {
        $storeSlug = $this->store->slug ?? 'STORE';
        $productSlug = Str::upper(Str::substr(Str::slug($this->name), 0, 8));
        $random = strtoupper(Str::random(4));

        return "{$storeSlug}-{$productSlug}-{$random}";
    }

    /**
     * Set historical cost data when cost price is provided.
     */
    private function setHistoricalCostData(): void
    {
        $this->historical_cost_currency = $this->base_currency;
        $this->historical_cost_amount = $this->cost_price;
        $this->historical_cost_rate = 1.0; // Base currency rate is 1:1
        $this->historical_cost_date = now()->format('Y-m-d');
    }

    /**
     * Record price change in history.
     */
    private function recordPriceChange(): void
    {
        $changes = [];

        if ($this->isDirty('base_price')) {
            $changes[] = [
                'currency' => $this->base_currency,
                'old_price' => $this->getOriginal('base_price'),
                'new_price' => $this->base_price,
            ];
        }

        if ($this->isDirty('base_sale_price')) {
            $changes[] = [
                'currency' => $this->base_currency,
                'old_price' => $this->getOriginal('base_sale_price'),
                'new_price' => $this->base_sale_price,
            ];
        }

        foreach ($changes as $change) {
            ProductPriceHistory::create([
                'product_id' => $this->id,
                'currency' => $change['currency'],
                'old_price' => $change['old_price'],
                'new_price' => $change['new_price'],
                'change_reason' => 'manual',
                'changed_by' => auth()->id(),
            ]);
        }
    }

    /**
     * Scope to get only active products.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get only featured products.
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    /**
     * Scope to get only in-stock products.
     */
    public function scopeInStock($query)
    {
        return $query->where(function ($q) {
            $q->where('manage_stock', false)
              ->orWhere('stock_quantity', '>', 0);
        });
    }

    /**
     * Scope to get products on sale.
     */
    public function scopeOnSale($query)
    {
        return $query->where('is_on_sale', true)
            ->where(function ($q) {
                $q->whereNull('sale_start_date')
                  ->orWhere('sale_start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('sale_end_date')
                  ->orWhere('sale_end_date', '>=', now());
            });
    }
}
