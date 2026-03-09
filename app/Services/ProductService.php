<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use App\Repositories\ProductRepository;
use App\Repositories\ProductCategoryRepository;
use App\Repositories\ProductTagRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProductService
{
    public function __construct(
        private ProductRepository $productRepo,
        private ProductCategoryRepository $categoryRepo,
        private ProductTagRepository $tagRepo
    ) {}
    
    /**
     * Get products for tenant with filters.
     */
    public function getProductsForTenant(Tenant $tenant, array $filters = []): LengthAwarePaginator
    {
        try {
            return $this->productRepo
                ->forTenant($tenant)
                ->withFilters($filters)
                ->paginate(15);
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
            return $this->categoryRepo->forTenant($tenant)->get();
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
            return $this->productRepo->getStatistics($tenant->id);
        } catch (\Exception $e) {
            throw new \Exception('Failed to retrieve statistics: ' . $e->getMessage());
        }
    }
    
    /**
     * Create product for tenant.
     */
    public function createProductForTenant(Tenant $tenant, User $user, array $data): Product
    {
        try {
            $data['tenant_id'] = $tenant->id;
            $data['created_by'] = $user->id;
            
            return $this->productRepo->create($data);
        } catch (\Exception $e) {
            throw new \Exception('Failed to create product: ' . $e->getMessage());
        }
    }
    
    /**
     * Get product by ID for tenant.
     */
    public function getProductForTenant(Tenant $tenant, int $productId): ?Product
    {
        try {
            return $this->productRepo
                ->forTenant($tenant)
                ->find($productId);
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
            return $this->productRepo
                ->forTenant($tenant)
                ->update($productId, $data);
        } catch (\Exception $e) {
            throw new \Exception('Failed to update product: ' . $e->getMessage());
        }
    }
    
    /**
     * Delete product for tenant.
     */
    public function deleteProductForTenant(Tenant $tenant, int $productId): bool
    {
        try {
            return $this->productRepo
                ->forTenant($tenant)
                ->delete($productId);
        } catch (\Exception $e) {
            throw new \Exception('Failed to delete product: ' . $e->getMessage());
        }
    }
}
