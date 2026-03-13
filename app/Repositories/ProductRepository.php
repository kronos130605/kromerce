<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProductRepository extends BaseRepository
{
    protected array $allowedFields = [
        'name', 'description', 'price', 'is_active', 'category_id',
        'store_id', 'created_at', 'updated_at'
    ];

    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    /**
     * Get products for store.
     */
    public function getForStore(int $storeId, array $filters = []): Collection
    {
        $query = $this->model->where('store_id', $storeId);

        $this->applyProductFilters($query, $filters);

        return $query->get();
    }

    /**
     * Get paginated products for store.
     */
    public function paginateForStore(int $storeId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->where('store_id', $storeId);

        $this->applyProductFilters($query, $filters);

        return $query->paginate($perPage);
    }

    /**
     * Get active products for store.
     */
    public function getActiveForStore(int $storeId): Collection
    {
        return $this->model
            ->where('store_id', $storeId)
            ->where('is_active', true)
            ->get();
    }

    /**
     * Get products with relationships.
     */
    public function getWithRelationships(int $storeId, array $relationships = []): Collection
    {
        $defaultRelationships = [
            'category',
            'tags',
            'images' => function ($query) {
                $query->orderBy('order');
            },
            'variants' => function ($query) {
                $query->where('is_active', true);
            },
        ];

        $relationships = array_merge($defaultRelationships, $relationships);

        return $this->model
            ->where('store_id', $storeId)
            ->with($relationships)
            ->get();
    }

    /**
     * Search products.
     */
    public function search(int $storeId, string $query, array $filters = []): Collection
    {
        return $this->model
            ->where('store_id', $storeId)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('sku', 'like', "%{$query}%");
            })
            ->when(isset($filters['category_id']), function ($q) use ($filters) {
                $q->where('category_id', $filters['category_id']);
            })
            ->when(isset($filters['min_price']), function ($q) use ($filters) {
                $q->where('base_price', '>=', $filters['min_price']);
            })
            ->when(isset($filters['max_price']), function ($q) use ($filters) {
                $q->where('base_price', '<=', $filters['max_price']);
            })
            ->get();
    }

    /**
     * Apply product filters.
     */
    private function applyProductFilters($query, array $filters): void
    {
        $query->when(isset($filters['category_id']), function ($q) use ($filters) {
            $q->where('category_id', $filters['category_id']);
        });

        $query->when(isset($filters['min_price']), function ($q) use ($filters) {
            $q->where('base_price', '>=', $filters['min_price']);
        });

        $query->when(isset($filters['max_price']), function ($q) use ($filters) {
            $q->where('base_price', '<=', $filters['max_price']);
        });

        $query->when(isset($filters['status']), function ($q) use ($filters) {
            $q->where('is_active', $filters['status'] === 'active');
        });

        $query->when(isset($filters['search']), function ($q) use ($filters) {
            $q->where('name', 'like', "%{$filters['search']}%")
              ->orWhere('description', 'like', "%{$filters['search']}%");
        });
    }
}
