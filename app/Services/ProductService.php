<?php

namespace App\Services;

use App\Factories\RepositoryFactory;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProductService
{
    public function __construct(
        private RepositoryFactory $repositoryFactory
    ) {}

    /**
     * Get products for store with filters.
     */
    public function getProductsForStore(Store $store, array $filters = []): LengthAwarePaginator
    {
        try {
            return $this->repositoryFactory->productRepository()->paginateForStore($store->id, $filters);
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
            return $this->repositoryFactory->productCategoryRepository()->getForStore($store->id);
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
            return $this->repositoryFactory->productRepository()->getStatistics($store->id);
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

            return $this->repositoryFactory->productRepository()->create($data);
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
            return $this->repositoryFactory->productRepository()
                ->getById($productId, ['store_id' => $store->id]);
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
            return $this->repositoryFactory->productRepository()
                ->update($productId, array_merge($data, ['store_id' => $store->id]));
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
            return $this->repositoryFactory->productRepository()->getLatestForStore($store->id, $limit);
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
            return $this->repositoryFactory->productRepository()->getLowStock($store->id)->take($limit);
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
            return $this->repositoryFactory->productRepository()
                ->delete($productId, ['store_id' => $store->id]);
        } catch (\Exception $e) {
            throw new \Exception('Failed to delete product: ' . $e->getMessage());
        }
    }
}
