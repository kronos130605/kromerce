<?php

namespace App\Repositories;

use App\Models\ProductTag;
use Illuminate\Database\Eloquent\Collection;

class ProductTagRepository extends BaseRepository
{
    public function __construct(ProductTag $model)
    {
        parent::__construct($model);
    }

    /**
     * Get tags for tenant.
     */
    public function getForTenant(string $tenantId): Collection
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get popular tags for tenant.
     */
    public function getPopularForTenant(string $tenantId, int $limit = 20): Collection
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->withCount('products')
            ->orderBy('products_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Search tags by name.
     */
    public function search(string $tenantId, string $query): Collection
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->where('name', 'like', "%{$query}%")
            ->orderBy('name')
            ->get();
    }

    /**
     * Get or create tag.
     */
    public function getOrCreate(string $tenantId, string $name): ProductTag
    {
        return $this->firstOrCreate(
            [
                'tenant_id' => $tenantId,
                'slug' => \Illuminate\Support\Str::slug($name),
            ],
            ['name' => $name]
        );
    }

    /**
     * Get tags with product count.
     */
    public function getWithProductCount(string $tenantId): Collection
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->withCount('products')
            ->orderBy('products_count', 'desc')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get unused tags for tenant.
     */
    public function getUnusedForTenant(string $tenantId): Collection
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->whereDoesntHave('products')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get tag statistics.
     */
    public function getStatistics(string $tenantId): array
    {
        $tags = $this->model->where('tenant_id', $tenantId);
        
        return [
            'total_tags' => $tags->count(),
            'used_tags' => $tags->whereHas('products')->count(),
            'unused_tags' => $tags->whereDoesntHave('products')->count(),
            'most_used' => $tags->withCount('products')->orderBy('products_count', 'desc')->first()?->products_count ?? 0,
        ];
    }

    /**
     * Batch create tags.
     */
    public function batchCreate(string $tenantId, array $tagNames): Collection
    {
        $tags = collect();
        
        foreach ($tagNames as $name) {
            $tag = $this->getOrCreate($tenantId, trim($name));
            $tags->push($tag);
        }
        
        return $tags;
    }

    /**
     * Clean up unused tags.
     */
    public function cleanupUnused(string $tenantId): int
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->whereDoesntHave('products')
            ->delete();
    }
}
