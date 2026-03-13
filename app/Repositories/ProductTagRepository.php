<?php

namespace App\Repositories;

use App\Models\ProductTag;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class ProductTagRepository extends BaseRepository
{
    protected array $allowedFields = [
        'name', 'slug', 'color', 'store_id', 'created_at', 'updated_at'
    ];

    public function __construct(ProductTag $model)
    {
        parent::__construct($model);
    }

    /**
     * Get tags for store.
     */
    public function getForStore(int $storeId): Collection
    {
        return $this->model
            ->where('store_id', $storeId)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get popular tags for store.
     */
    public function getPopularForStore(int $storeId, int $limit = 20): Collection
    {
        return $this->model
            ->where('store_id', $storeId)
            ->withCount('products')
            ->orderBy('products_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Search tags by name.
     */
    public function search(int $storeId, string $query): Collection
    {
        return $this->model
            ->where('store_id', $storeId)
            ->where('name', 'like', "%{$query}%")
            ->orderBy('name')
            ->get();
    }
}
