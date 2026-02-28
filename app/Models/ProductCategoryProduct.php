<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductCategoryProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'category_id',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    /**
     * Get the product that owns the pivot.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the category that owns the pivot.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class);
    }

    /**
     * Scope to get products in a specific order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
