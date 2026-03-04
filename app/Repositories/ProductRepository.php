<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProductRepository extends BaseRepository
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    /**
     * Create new product.
     */
    public function create(array $data): Product
    {
        return $this->model->create($data);
    }

    /**
     * Get products for tenant.
     */
    public function getForTenant(string $tenantId, array $filters = []): Collection
    {
        $query = $this->model->where('tenant_id', $tenantId);

        $this->applyProductFilters($query, $filters);

        return $query->get();
    }

    /**
     * Get paginated products for tenant.
     */
    public function paginateForTenant(string $tenantId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->where('tenant_id', $tenantId);

        $this->applyProductFilters($query, $filters);

        return $query->paginate($perPage);
    }

    /**
     * Get active products for tenant.
     */
    public function getActiveForTenant(string $tenantId): Collection
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->get();
    }

    /**
     * Get products with relationships.
     */
    public function getWithRelationships(string $tenantId, array $relationships = []): Collection
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
            ->where('tenant_id', $tenantId)
            ->with($relationships)
            ->get();
    }

    /**
     * Search products.
     */
    public function search(string $tenantId, string $query, array $filters = []): Collection
    {
        return $this->model
            ->where('tenant_id', $tenantId)
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
     * Get products by category.
     */
    public function getByCategory(string $tenantId, int $categoryId): Collection
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->where('category_id', $categoryId)
            ->get();
    }

    /**
     * Get products on sale.
     */
    public function getOnSale(string $tenantId): Collection
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->where('is_on_sale', true)
            ->where('is_active', true)
            ->get();
    }

    /**
     * Get products with low stock.
     */
    public function getLowStock(string $tenantId): Collection
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->where('manage_stock', true)
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->get();
    }

    /**
     * Get out of stock products.
     */
    public function getOutOfStock(string $tenantId): Collection
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->where('manage_stock', true)
            ->where('stock_quantity', 0)
            ->get();
    }

    /**
     * Duplicate product.
     */
    public function duplicate(int $productId, array $overrides = []): Product
    {
        $originalProduct = $this->getById($productId);

        if (!$originalProduct) {
            throw new \Exception("Product not found");
        }

        $productData = $originalProduct->toArray();

        // Remove fields that should be unique
        unset($productData['id'], $productData['sku'], $productData['slug'], $productData['created_at'], $productData['updated_at']);

        // Apply overrides
        $productData = array_merge($productData, $overrides);

        // Generate new SKU and slug
        $productData['sku'] = $this->generateUniqueSku($productData['name']);
        $productData['slug'] = $this->generateUniqueSlug($productData['name']);

        return $this->create($productData);
    }

    /**
     * Get product statistics for tenant.
     */
    public function getStatistics(string $tenantId): array
    {
        $products = $this->model->where('tenant_id', $tenantId);

        return [
            'total_products' => $products->count(),
            'active_products' => $products->where('status', 'active')->count(),
            'inactive_products' => $products->where('status', 'inactive')->count(),
            'draft_products' => $products->where('status', 'draft')->count(),
            'products_on_sale' => $products->where('is_on_sale', true)->count(),
            'featured_products' => $products->where('featured', true)->count(),
            'out_of_stock' => $products->where('manage_stock', true)->where('stock_quantity', 0)->count(),
            'low_stock' => $products->where('manage_stock', true)->whereColumn('stock_quantity', '<=', 'low_stock_threshold')->count(),
        ];
    }

    /**
     * Apply product-specific filters.
     */
    private function applyProductFilters($query, array $filters): void
    {
        $query->when(isset($filters['category_id']), function ($q) use ($filters) {
            $q->where('category_id', $filters['category_id']);
        })
        ->when(isset($filters['status']), function ($q) use ($filters) {
            $q->where('status', $filters['status']);
        })
        ->when(isset($filters['is_on_sale']), function ($q) use ($filters) {
            $q->where('is_on_sale', $filters['is_on_sale']);
        })
        ->when(isset($filters['min_price']), function ($q) use ($filters) {
            $q->where('base_price', '>=', $filters['min_price']);
        })
        ->when(isset($filters['max_price']), function ($q) use ($filters) {
            $q->where('base_price', '<=', $filters['max_price']);
        })
        ->when(isset($filters['manage_stock']), function ($q) use ($filters) {
            $q->where('manage_stock', $filters['manage_stock']);
        })
        ->when(isset($filters['in_stock']), function ($q) use ($filters) {
            if ($filters['in_stock']) {
                $q->where(function ($subQuery) {
                    $subQuery->where('manage_stock', false)
                           ->orWhere('stock_quantity', '>', 0);
                });
            }
        });
    }

    /**
     * Generate unique SKU.
     */
    private function generateUniqueSku(string $productName): string
    {
        $baseSku = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $productName), 0, 8));
        $sku = $baseSku;
        $counter = 1;

        while ($this->model->where('sku', $sku)->exists()) {
            $sku = $baseSku . str_pad($counter, 3, '0', STR_PAD_LEFT);
            $counter++;
        }

        return $sku;
    }

    /**
     * Generate unique slug.
     */
    private function generateUniqueSlug(string $productName): string
    {
        $baseSlug = \Illuminate\Support\Str::slug($productName);
        $slug = $baseSlug;
        $counter = 1;

        while ($this->model->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Count products for tenant.
     */
    public function countForTenant(string $tenantId): int
    {
        return $this->model->where('tenant_id', $tenantId)->count();
    }

    /**
     * Count active products for tenant.
     */
    public function countActiveForTenant(string $tenantId): int
    {
        return $this->model->where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->count();
    }

    /**
     * Count low stock products for tenant.
     */
    public function countLowStockForTenant(string $tenantId): int
    {
        return $this->model->where('tenant_id', $tenantId)
            ->where('manage_stock', true)
            ->where('stock_quantity', '<=', DB::raw('low_stock_threshold'))
            ->count();
    }

    /**
     * Get recent price changes for tenant.
     */
    public function getRecentPriceChanges(string $tenantId, int $limit = 10): Collection
    {
        $items = DB::table('product_price_history as pph')
            ->join('products as p', 'pph.product_id', '=', 'p.id')
            ->where('p.tenant_id', $tenantId)
            ->orderBy('pph.created_at', 'desc')
            ->limit($limit)
            ->get([
                'pph.*',
                'p.name as product_name',
                'p.sku as product_sku'
            ]);

        return new Collection($items);

    }

    /**
     * Get recent stock updates for tenant.
     */
    public function getRecentStockUpdates(string $tenantId, int $limit = 5): Collection
    {
        // Esto requeriría una tabla de log de stock, por ahora retornamos productos con stock bajo
        return $this->model->where('tenant_id', $tenantId)
            ->where('manage_stock', true)
            ->where('stock_quantity', '<=', DB::raw('low_stock_threshold'))
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get(['id', 'name', 'sku', 'stock_quantity', 'low_stock_threshold', 'updated_at']);
    }

    /**
     * Get top products by revenue for tenant.
     */
    public function getTopByRevenue(string $tenantId, int $limit = 5): Collection
    {
        // Esto requeriría tabla de orders, por ahora retornamos productos más caros
        return $this->model->where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->orderBy('base_price', 'desc')
            ->limit($limit)
            ->get(['id', 'name', 'sku', 'base_price', 'base_currency']);
    }

    /**
     * Get top products by views for tenant.
     */
    public function getTopByViews(string $tenantId, int $limit = 5): Collection
    {
        // Esto requeriría sistema de analytics, por ahora retornamos productos más recientes
        return $this->model->where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get(['id', 'name', 'sku', 'created_at']);
    }

    /**
     * Get top products by sales for tenant.
     */
    public function getTopBySales(string $tenantId, int $limit = 5): Collection
    {
        // Esto requeriría tabla de orders, por ahora retornamos productos destacados
        return $this->model->where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->where('featured', true)
            ->limit($limit)
            ->get(['id', 'name', 'sku', 'featured']);
    }

    /**
     * Get monthly revenue for tenant.
     */
    public function getMonthlyRevenue(string $tenantId, int $months = 12): array
    {
        // Datos mock para desarrollo
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $data[] = [
                'month' => $date->format('M Y'),
                'revenue' => rand(10000, 50000),
            ];
        }
        return $data;
    }

    /**
     * Get product growth for tenant.
     */
    public function getProductGrowth(string $tenantId, int $months = 6): array
    {
        $data = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $data[] = [
                'month' => $date->format('M Y'),
                'products' => rand(50, 150),
            ];
        }
        return $data;
    }

    /**
     * Get latest products for tenant.
     */
    public function getLatestForTenant(string $tenantId, int $limit = 5): Collection
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get(['id', 'name', 'sku', 'base_price', 'base_currency', 'created_at']);
    }
}
