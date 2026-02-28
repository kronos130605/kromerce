<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductVariantImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'variant_id',
        'url',
        'alt',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    /**
     * Get the variant that owns the image.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    /**
     * Get the full URL for the image.
     */
    public function getFullUrlAttribute(): string
    {
        return asset($this->url);
    }

    /**
     * Scope to order by order field.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
