<?php

namespace App\Services;

use App\Http\Resources\ProductResource;
use App\Models\Store;
use App\Models\User;
use App\Repositories\Product\ProductCategoryRepository;
use App\Repositories\Product\ProductRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProductService
{
    public function __construct(
        private ProductRepository $productRepository,
        private ProductCategoryRepository $productCategoryRepository
    ) {}

    /**
     * Get products for store with filters.
     */
    public function getProductsForStore(Store $store, array $filters = []): LengthAwarePaginator
    {
        try {
            $paginator = $this->productRepository->paginateForStore($store->id, $filters);
            
            // Transform items using ProductResource to ensure proper serialization
            $transformedItems = ProductResource::collection($paginator->getCollection())->toArray(request());
            
            return new LengthAwarePaginator(
                $transformedItems,
                $paginator->total(),
                $paginator->perPage(),
                $paginator->currentPage(),
                ['path' => $paginator->path()]
            );
        } catch (\Exception $e) {
            throw new \Exception('Failed to retrieve products: ' . $e->getMessage());
        }
    }

    /**
     * Get categories for store.
     */
    public function getCategoriesForStore(Store $store): Collection
    {
        try {
            return $this->productCategoryRepository->getForStore($store->id);
        } catch (\Exception $e) {
            throw new \Exception('Failed to retrieve categories: ' . $e->getMessage());
        }
    }

    /**
     * Get product statistics for store.
     */
    public function getStatisticsForStore(Store $store): array
    {
        try {
            return $this->productRepository->getStatistics($store->id);
        } catch (\Exception $e) {
            throw new \Exception('Failed to retrieve statistics: ' . $e->getMessage());
        }
    }

    /**
     * Create product for store.
     */
    public function createProductForStore(Store $store, User $user, array $data): Model
    {
        try {
            $data['store_id'] = $store->id;
            $data['created_by'] = $user->id;

            return $this->productRepository->create($data);
        } catch (\Exception $e) {
            throw new \Exception('Failed to create product: ' . $e->getMessage());
        }
    }

    /**
     * Get product by ID for store.
     */
    public function getProductForStore(Store $store, int $productId): ?Model
    {
        try {
            return $this->productRepository
                ->getFirstBy(['id' => $productId, 'store_id' => $store->id]);
        } catch (\Exception $e) {
            throw new \Exception('Failed to retrieve product: ' . $e->getMessage());
        }
    }

    /**
     * Update product for store.
     */
    public function updateProductForStore(Store $store, int $productId, array $data): bool
    {
        try {
            $updated = $this->productRepository
                ->updateBy(['id' => $productId, 'store_id' => $store->id], $data);

            return $updated > 0;
        } catch (\Exception $e) {
            throw new \Exception('Failed to update product: ' . $e->getMessage());
        }
    }

    /**
     * Get latest products for store.
     */
    public function getLatestProductsForStore(Store $store, int $limit = 5): Collection
    {
        try {
            return $this->productRepository->getLatestForStore($store->id, $limit);
        } catch (\Exception $e) {
            throw new \Exception('Failed to retrieve latest products: ' . $e->getMessage());
        }
    }

    /**
     * Get low stock products for store.
     */
    public function getLowStockProductsForStore(Store $store, int $limit = 5): Collection
    {
        try {
            return $this->productRepository->getLowStock($store->id)->take($limit);
        } catch (\Exception $e) {
            throw new \Exception('Failed to retrieve low stock products: ' . $e->getMessage());
        }
    }

    /**
     * Delete product for store.
     */
    public function deleteProductForStore(Store $store, int $productId): bool
    {
        try {
            $deleted = $this->productRepository
                ->deleteBy(['id' => $productId, 'store_id' => $store->id]);

            return $deleted > 0;
        } catch (\Exception $e) {
            throw new \Exception('Failed to delete product: ' . $e->getMessage());
        }
    }
}
