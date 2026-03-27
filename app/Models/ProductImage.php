<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'url',
        'thumbnail_url',
        'medium_url',
        'alt',
        'title',
        'order',
        'is_primary',
        'metadata',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'order' => 'integer',
        'metadata' => 'array',
    ];

    /**
     * Get the product that owns the image.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Set this image as primary and update other images.
     */
    public function setAsPrimary(): void
    {
        // Remove primary status from other images
        static::where('product_id', $this->product_id)
            ->where('id', '!=', $this->id)
            ->update(['is_primary' => false]);

        // Set this image as primary
        $this->update(['is_primary' => true]);
    }

    /**
     * Get the full URL for the image.
     */
    public function getFullUrlAttribute(): string
    {
        return asset($this->url);
    }

    /**
     * Get the thumbnail URL (if available).
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        if (isset($this->metadata['thumbnail'])) {
            return asset($this->metadata['thumbnail']);
        }

        // Generate thumbnail path (assuming naming convention)
        $pathInfo = pathinfo($this->url);
        $thumbnail = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];
        
        return file_exists(public_path($thumbnail)) ? asset($thumbnail) : null;
    }

    /**
     * Get image dimensions from metadata.
     */
    public function getDimensionsAttribute(): ?array
    {
        return $this->metadata['dimensions'] ?? null;
    }

    /**
     * Get file size from metadata.
     */
    public function getFileSizeAttribute(): ?int
    {
        return $this->metadata['file_size'] ?? null;
    }

    /**
     * Scope to get primary images.
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope to order by order field.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
