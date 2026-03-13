<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ProductCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'description',
        'image',
        'parent_id',
        'level',
        'order',
        'status',
        'settings',
    ];

    protected $casts = [
        'level' => 'integer',
        'order' => 'integer',
        'settings' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }

            // Set level based on parent
            if ($category->parent_id) {
                $parent = static::find($category->parent_id);
                $category->level = $parent ? $parent->level + 1 : 0;
            } else {
                $category->level = 0;
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && !$category->isDirty('slug')) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * Get the store that owns the category.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the parent category.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    /**
     * Get the child categories.
     */
    public function children(): HasMany
    {
        return $this->hasMany(ProductCategory::class, 'parent_id')
            ->orderBy('order');
    }

    /**
     * Get the products in this category.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_category_product')
            ->withPivot('order')
            ->withTimestamps()
            ->orderBy('pivot_order');
    }

    /**
     * Get all descendants (recursive).
     */
    public function descendants(): HasMany
    {
        return $this->hasMany(ProductCategory::class, 'parent_id')
            ->with('descendants');
    }

    /**
     * Get all ancestors (recursive).
     */
    public function ancestors()
    {
        $ancestors = collect();
        $parent = $this->parent;

        while ($parent) {
            $ancestors->push($parent);
            $parent = $parent->parent;
        }

        return $ancestors;
    }

    /**
     * Get the full path of the category.
     */
    public function getFullPath(): string
    {
        $ancestors = $this->ancestors()->reverse();
        $path = $ancestors->pluck('name')->push($this->name);

        return $path->implode(' > ');
    }

    /**
     * Get the breadcrumb path.
     */
    public function getBreadcrumbPath(): array
    {
        $ancestors = $this->ancestors()->reverse();
        $breadcrumbs = [];

        foreach ($ancestors as $ancestor) {
            $breadcrumbs[] = [
                'name' => $ancestor->name,
                'slug' => $ancestor->slug,
                'url' => route('products.category', $ancestor->slug),
            ];
        }

        $breadcrumbs[] = [
            'name' => $this->name,
            'slug' => $this->slug,
            'url' => route('products.category', $this->slug),
            'current' => true,
        ];

        return $breadcrumbs;
    }

    /**
     * Get the total number of products in this category and its children.
     */
    public function getTotalProductsCount(): int
    {
        $categoryIds = $this->descendants->pluck('id')->push($this->id);

        return ProductCategoryProduct::whereIn('category_id', $categoryIds)
            ->distinct('product_id')
            ->count('product_id');
    }

    /**
     * Scope to get only active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get root categories (no parent).
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope to get categories at a specific level.
     */
    public function scopeLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Check if category has children.
     */
    public function hasChildren(): bool
    {
        return $this->children()->count() > 0;
    }

    /**
     * Check if category is a leaf category (no children).
     */
    public function isLeaf(): bool
    {
        return !$this->hasChildren();
    }
}
