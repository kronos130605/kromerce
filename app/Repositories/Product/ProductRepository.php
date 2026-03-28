<?php

namespace App\Repositories\Product;

use App\Models\Product;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class ProductRepository extends BaseRepository
{
    protected array $allowedFields = [
        'id',
        'name', 'slug', 'description', 'short_description', 'base_currency', 
        'base_price', 'base_sale_price', 'cost_price', 'is_on_sale', 
        'sale_type', 'sale_discount', 'sale_start_date', 'sale_end_date',
        'sku', 'barcode', 'status', 'visibility', 'featured', 
        'downloadable', 'virtual', 'product_type', 'manage_stock', 
        'stock_quantity', 'stock_status', 'low_stock_threshold', 
        'store_id', 'category_id', 'created_at', 'updated_at'
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
        $query = $this->model->newQuery()->where('store_id', $storeId);

        $this->applyProductFilters($query, $filters);

        return $query->get();
    }

    /**
     * Get paginated products for store.
     */
    public function paginateForStore(int $storeId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->where('store_id', $storeId);

        $this->applyProductFilters($query, $filters);

        return $query->paginate($perPage);
    }

    /**
     * Get active products for store.
     */
    public function getActiveForStore(int $storeId): Collection
    {
        return $this->model->newQuery()
            ->where('store_id', $storeId)
            ->where('status', 'active')
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

        return $this->model->newQuery()
            ->where('store_id', $storeId)
            ->with($relationships)
            ->get();
    }

    /**
     * Search products.
     */
    public function search(int $storeId, string $query, array $filters = []): Collection
    {
        return $this->model->newQuery()
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
            $q->where('status', $filters['status']);
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
        return $this->model->newQuery()->where('store_id', $storeId)->count();
    }

    /**
     * Count active products for store.
     */
    public function countActiveForStore(int $storeId): int
    {
        return $this->model->newQuery()
            ->where('store_id', $storeId)
            ->where('status', 'active')
            ->count();
    }

    /**
     * Count low stock products for store.
     */
    public function countLowStockForStore(int $storeId): int
    {
        return $this->model->newQuery()
            ->where('store_id', $storeId)
            ->where('manage_stock', true)
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->count();
    }

    /**
     * Get low stock products for store.
     */
    public function getLowStock(int $storeId): Collection
    {
        return $this->model->newQuery()
            ->where('store_id', $storeId)
            ->where('manage_stock', true)
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->orderBy('stock_quantity', 'asc')
            ->get();
    }

    /**
     * Get latest products for store.
     */
    public function getLatestForStore(int $storeId, int $limit = 5): Collection
    {
        return $this->model->newQuery()
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
        return $this->model->newQuery()
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
        return $this->model->newQuery()
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
        return $this->model->newQuery()
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
        return $this->model->newQuery()
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
        return $this->model->newQuery()
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

    /**
     * Get product statistics for store.
     */
    public function getStatistics(int $storeId): array
    {
        try {
            $stats = [
                'total_products' => $this->model->newQuery()->where('store_id', $storeId)->count(),
                'active_products' => $this->model->newQuery()->where('store_id', $storeId)->where('status', 'active')->count(),
                'inactive_products' => $this->model->newQuery()->where('store_id', $storeId)->where('status', '!=', 'active')->count(),
                'total_value' => $this->model->newQuery()->where('store_id', $storeId)->sum('base_price'),
                'average_price' => $this->model->newQuery()->where('store_id', $storeId)->avg('base_price'),
                'recent_products' => $this->model->newQuery()->where('store_id', $storeId)
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->count(),
            ];

            // Get products by category if categories exist
            try {
                $categoryStats = \DB::table('products')
                    ->join('product_categories', 'products.category_id', '=', 'product_categories.id')
                    ->where('products.store_id', $storeId)
                    ->groupBy('product_categories.name')
                    ->selectRaw('product_categories.name as category, COUNT(*) as count')
                    ->get()
                    ->pluck('count', 'category')
                    ->toArray();

                $stats['products_by_category'] = $categoryStats;
            } catch (\Exception $e) {
                $stats['products_by_category'] = [];
            }

            return $stats;
        } catch (\Exception $e) {
            Log::error('Error getting product statistics', [
                'store_id' => $storeId,
                'error' => $e->getMessage(),
            ]);
            
            return [
                'total_products' => 0,
                'active_products' => 0,
                'inactive_products' => 0,
                'total_value' => 0,
                'average_price' => 0,
                'recent_products' => 0,
                'products_by_category' => [],
            ];
        }
    }
}
