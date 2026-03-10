<?php

namespace App\Services;

use App\Factories\RepositoryFactory;
use App\Models\Tenant;
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
     * Get products for tenant with filters.
     */
    public function getProductsForTenant(Tenant $tenant, array $filters = []): LengthAwarePaginator
    {
        try {
            return $this->repositoryFactory->productRepository()->paginateForTenant($tenant->id, $filters);
        } catch (\Exception $e) {
            throw new \Exception('Failed to retrieve products: ' . $e->getMessage());
        }
    }

    /**
     * Get categories for tenant.
     */
    public function getCategoriesForTenant(Tenant $tenant): Collection
    {
        try {
            return $this->repositoryFactory->productCategoryRepository()->getForTenant($tenant->id);
        } catch (\Exception $e) {
            throw new \Exception('Failed to retrieve categories: ' . $e->getMessage());
        }
    }

    /**
     * Get product statistics for tenant.
     */
    public function getStatisticsForTenant(Tenant $tenant): array
    {
        try {
            return $this->repositoryFactory->productRepository()->getStatistics($tenant->id);
        } catch (\Exception $e) {
            throw new \Exception('Failed to retrieve statistics: ' . $e->getMessage());
        }
    }

    /**
     * Create product for tenant.
     */
    public function createProductForTenant(Tenant $tenant, User $user, array $data): Model
    {
        try {
            $data['tenant_id'] = $tenant->id;
            $data['created_by'] = $user->id;

            return $this->repositoryFactory->productRepository()->create($data);
        } catch (\Exception $e) {
            throw new \Exception('Failed to create product: ' . $e->getMessage());
        }
    }

    /**
     * Get product by ID for tenant.
     */
    public function getProductForTenant(Tenant $tenant, int $productId): ?Model
    {
        try {
            return $this->repositoryFactory->productRepository()
                ->getById($productId, ['tenant_id' => $tenant->id]);
        } catch (\Exception $e) {
            throw new \Exception('Failed to retrieve product: ' . $e->getMessage());
        }
    }

    /**
     * Update product for tenant.
     */
    public function updateProductForTenant(Tenant $tenant, int $productId, array $data): bool
    {
        try {
            return $this->repositoryFactory->productRepository()
                ->update($productId, array_merge($data, ['tenant_id' => $tenant->id]));
        } catch (\Exception $e) {
            throw new \Exception('Failed to update product: ' . $e->getMessage());
        }
    }

    /**
     * Get latest products for tenant.
     */
    public function getLatestProductsForTenant(Tenant $tenant, int $limit = 5): Collection
    {
        try {
            return $this->repositoryFactory->productRepository()->getLatestForTenant($tenant->id, $limit);
        } catch (\Exception $e) {
            throw new \Exception('Failed to retrieve latest products: ' . $e->getMessage());
        }
    }

    /**
     * Get low stock products for tenant.
     */
    public function getLowStockProductsForTenant(Tenant $tenant, int $limit = 5): Collection
    {
        try {
            return $this->repositoryFactory->productRepository()->getLowStock($tenant->id)->take($limit);
        } catch (\Exception $e) {
            throw new \Exception('Failed to retrieve low stock products: ' . $e->getMessage());
        }
    }

    /**
     * Delete product for tenant.
     */
    public function deleteProductForTenant(Tenant $tenant, int $productId): bool
    {
        try {
            return $this->repositoryFactory->productRepository()
                ->delete($productId, ['tenant_id' => $tenant->id]);
        } catch (\Exception $e) {
            throw new \Exception('Failed to delete product: ' . $e->getMessage());
        }
    }
}
