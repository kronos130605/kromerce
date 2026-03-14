<?php

namespace App\Repositories\Product;

use App\Models\Product;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

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

    /**
     * Count products for store.
     */
    public function countForStore(int $storeId): int
    {
        return $this->model->where('store_id', $storeId)->count();
    }

    /**
     * Count active products for store.
     */
    public function countActiveForStore(int $storeId): int
    {
        return $this->model
            ->where('store_id', $storeId)
            ->where('is_active', true)
            ->count();
    }

    /**
     * Count low stock products for store.
     */
    public function countLowStockForStore(int $storeId): int
    {
        return $this->model
            ->where('store_id', $storeId)
            ->where('manage_stock', true)
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->count();
    }

    /**
     * Get latest products for store.
     */
    public function getLatestForStore(int $storeId, int $limit = 5): Collection
    {
        return $this->model
            ->where('store_id', $storeId)
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Get recent price changes for store.
     */
    public function getRecentPriceChanges(int $storeId, int $limit = 10): Collection
    {
        // This would typically track price history, for now return recent products
        return $this->model
            ->where('store_id', $storeId)
            ->orderBy('updated_at', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Get recent stock updates for store.
     */
    public function getRecentStockUpdates(int $storeId, int $limit = 5): Collection
    {
        return $this->model
            ->where('store_id', $storeId)
            ->where('manage_stock', true)
            ->orderBy('updated_at', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Get top products by revenue for store.
     */
    public function getTopByRevenue(int $storeId, int $limit = 5): Collection
    {
        return $this->model
            ->where('store_id', $storeId)
            ->orderBy('base_price', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Get top products by views for store.
     */
    public function getTopByViews(int $storeId, int $limit = 5): Collection
    {
        // This would typically track views, for now return recent products
        return $this->model
            ->where('store_id', $storeId)
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Get top products by sales for store.
     */
    public function getTopBySales(int $storeId, int $limit = 5): Collection
    {
        // This would typically track sales, for now return recent products
        return $this->model
            ->where('store_id', $storeId)
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Get monthly revenue for store.
     */
    public function getMonthlyRevenue(int $storeId, int $months = 12): array
    {
        // This would typically calculate actual revenue, for now return empty array
        return [];
    }

    /**
     * Get product growth for store.
     */
    public function getProductGrowth(int $storeId, int $months = 6): array
    {
        // This would typically calculate growth metrics, for now return empty array
        return [];
    }
}
