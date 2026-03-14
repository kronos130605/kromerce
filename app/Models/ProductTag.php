<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ProductTag extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'store_id',
        'name',
        'slug',
        'description',
        'color',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });

        static::updating(function ($tag) {
            if ($tag->isDirty('name') && !$tag->isDirty('slug')) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    /**
     * Get the store that owns the tag.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the products that have this tag.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_product_tag')
            ->withTimestamps();
    }

    /**
     * Get the total number of products with this tag.
     */
    public function getProductsCount(): int
    {
        return $this->products()->count();
    }

    /**
     * Scope to search tags by name.
     */
    public function scopeSearch($query, $term)
    {
        return $query->where('name', 'LIKE', "%{$term}%")
            ->orWhere('description', 'LIKE', "%{$term}%");
    }

    /**
     * Get popular tags (most used).
     */
    public static function getPopularTags(int $limit = 10): array
    {
        return ProductProductTag::selectRaw('tag_id, COUNT(*) as product_count')
            ->groupBy('tag_id')
            ->orderBy('product_count', 'desc')
            ->limit($limit)
            ->with('tag')
            ->get()
            ->map(function ($item) {
                return [
                    'tag' => $item->tag,
                    'product_count' => $item->product_count,
                ];
            })
            ->toArray();
    }
}
