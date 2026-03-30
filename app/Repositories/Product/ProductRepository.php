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
     * Get product by ID for specific store.
     */
    public function getByIdForStore(string $productId, int $storeId): ?Product
    {
        return $this->model->newQuery()
            ->where('id', $productId)
            ->where('store_id', $storeId)
            ->first();
    }

    /**
     * Update product by ID.
     */
    public function updateById(string $productId, array $data): bool
    {
        return $this->model->newQuery()
            ->where('id', $productId)
            ->update($data) > 0;
    }

    /**
     * Delete product by ID.
     */
    public function deleteById(string $productId): bool
    {
        return $this->model->newQuery()
            ->where('id', $productId)
            ->delete() > 0;
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
        $query = $this->model->newQuery()
            ->where('store_id', $storeId)
            ->with([
                'categories',
                'tags',
                'images' => function ($query) {
                    $query->orderBy('order');
                },
            ]);

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
                $q->whereHas('categories', function ($categoryQuery) use ($filters) {
                    $categoryQuery->where('product_categories.id', $filters['category_id']);
                });
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
     * Get trending products (for storefront).
     */
    public function getTrendingProducts(int $limit = 12): Collection
    {
        return $this->model->newQuery()
            ->where('status', 'active')
            ->with([
                'images:id,product_id,url,thumbnail_url,is_primary,order',
                'store:id,name,slug',
                'categories:id,name,slug'
            ])
            ->select(['id', 'name', 'slug', 'base_price', 'base_sale_price', 'base_currency', 'stock_quantity', 'featured', 'store_id'])
            ->inRandomOrder() // TODO: Replace with actual trending logic based on views/sales
            ->limit($limit)
            ->get();
    }

    /**
     * Get new arrivals (for storefront).
     */
    public function getNewArrivals(int $limit = 12, int $days = 30): Collection
    {
        return $this->model->newQuery()
            ->where('status', 'active')
            ->where('created_at', '>=', now()->subDays($days))
            ->with([
                'images:id,product_id,url,thumbnail_url,is_primary,order',
                'store:id,name,slug',
                'categories:id,name,slug'
            ])
            ->select(['id', 'name', 'slug', 'base_price', 'base_sale_price', 'base_currency', 'stock_quantity', 'featured', 'store_id', 'created_at'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get deals of the day (products with sale price).
     */
    public function getDealsOfTheDay(int $limit = 12): Collection
    {
        return $this->model->newQuery()
            ->where('status', 'active')
            ->whereNotNull('base_sale_price')
            ->whereColumn('base_sale_price', '<', 'base_price')
            ->with([
                'images:id,product_id,url,thumbnail_url,is_primary,order',
                'store:id,name,slug',
                'categories:id,name,slug'
            ])
            ->select(['id', 'name', 'slug', 'base_price', 'base_sale_price', 'base_currency', 'stock_quantity', 'featured', 'store_id'])
            ->inRandomOrder()
            ->limit($limit)
            ->get();
    }

    /**
     * Get products with filters for storefront.
     */
    public function getProductsWithFilters(array $filters, int $perPage = 24): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
            ->where('status', 'active')
            ->with(['images', 'store', 'categories']);

        // Apply filters
        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['category'])) {
            $query->whereHas('categories', function ($q) use ($filters) {
                $q->where('product_categories.id', $filters['category']);
            });
        }

        if (!empty($filters['store'])) {
            $query->where('store_id', $filters['store']);
        }

        if (!empty($filters['min_price'])) {
            $query->where('base_price', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('base_price', '<=', $filters['max_price']);
        }

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Get products by category.
     */
    public function getProductsByCategory(string $categoryId, array $filters, int $perPage = 24): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
            ->where('status', 'active')
            ->whereHas('categories', function ($q) use ($categoryId) {
                $q->where('product_categories.id', $categoryId);
            })
            ->with(['images', 'store', 'categories']);

        // Apply additional filters
        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['store'])) {
            $query->where('store_id', $filters['store']);
        }

        if (!empty($filters['min_price'])) {
            $query->where('base_price', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('base_price', '<=', $filters['max_price']);
        }

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($perPage);
    }

    /**
     * Search products globally.
     */
    public function searchProducts(string $query, int $perPage = 24): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->where('status', 'active')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                  ->orWhere('description', 'like', '%' . $query . '%')
                  ->orWhere('sku', 'like', '%' . $query . '%');
            })
            ->with(['images', 'store', 'categories'])
            ->paginate($perPage);
    }

    /**
     * Get related products (same categories).
     */
    public function getRelatedProducts(string $productId, int $limit = 8): Collection
    {
        $product = $this->getById($productId);
        
        if (!$product) {
            return new Collection();
        }

        $categoryIds = $product->categories->pluck('id')->toArray();

        return $this->model->newQuery()
            ->where('status', 'active')
            ->where('id', '!=', $productId)
            ->whereHas('categories', function ($q) use ($categoryIds) {
                $q->whereIn('product_categories.id', $categoryIds);
            })
            ->with(['images', 'store'])
            ->limit($limit)
            ->get();
    }

    /**
     * Get more products from same store.
     */
    public function getStoreProducts(string $storeId, ?string $excludeProductId = null, int $limit = 8): Collection
    {
        $query = $this->model->newQuery()
            ->where('status', 'active')
            ->where('store_id', $storeId)
            ->with(['images']);

        if ($excludeProductId) {
            $query->where('id', '!=', $excludeProductId);
        }

        return $query->limit($limit)
            ->with(['categories', 'tags', 'images'])
            ->get();
    }

    /**
     * Apply product filters.
     */
    private function applyProductFilters($query, array $filters): void
    {
        // Filter by category using relationship
        $query->when(isset($filters['category_id']), function ($q) use ($filters) {
            $q->whereHas('categories', function ($categoryQuery) use ($filters) {
                $categoryQuery->where('product_categories.id', $filters['category_id']);
            });
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

        // Fix search to not break other filters
        $query->when(isset($filters['search']), function ($q) use ($filters) {
            $q->where(function ($searchQuery) use ($filters) {
                $searchQuery->where('name', 'like', "%{$filters['search']}%")
                    ->orWhere('description', 'like', "%{$filters['search']}%")
                    ->orWhere('sku', 'like', "%{$filters['search']}%");
            });
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
            // Single optimized query for main stats
            $mainStats = \DB::table('products')
                ->where('store_id', $storeId)
                ->selectRaw('
                    COUNT(*) as total_products,
                    SUM(CASE WHEN status = "active" THEN 1 ELSE 0 END) as active_products,
                    SUM(CASE WHEN status != "active" THEN 1 ELSE 0 END) as inactive_products,
                    SUM(base_price) as total_value,
                    AVG(base_price) as average_price
                ')
                ->first();

            $stats = [
                'total_products' => (int) $mainStats->total_products,
                'active_products' => (int) $mainStats->active_products,
                'inactive_products' => (int) $mainStats->inactive_products,
                'total_value' => (float) $mainStats->total_value,
                'average_price' => (float) $mainStats->average_price,
            ];

            // Get products by category using many-to-many relationship
            try {
                $categoryStats = \DB::table('product_category_product')
                    ->join('product_categories', 'product_category_product.category_id', '=', 'product_categories.id')
                    ->join('products', 'product_category_product.product_id', '=', 'products.id')
                    ->where('products.store_id', $storeId)
                    ->groupBy('product_categories.id', 'product_categories.name')
                    ->selectRaw('product_categories.name as category, COUNT(DISTINCT products.id) as count')
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
                'products_by_category' => [],
            ];
        }
    }
}
