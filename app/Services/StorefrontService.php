<?php

namespace App\Services;

use App\Repositories\Product\ProductRepository;
use App\Repositories\Product\ProductCategoryRepository;
use App\Repositories\Store\StoreRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class StorefrontService
{
    public function __construct(
        private ProductRepository $productRepository,
        private ProductCategoryRepository $categoryRepository,
        private StoreRepository $storeRepository
    ) {}

    /**
     * Get data for marketplace home page.
     */
    public function getHomePageData(): array
    {
        try {
            return [
                'featured_categories' => $this->getFeaturedCategories() ?? collect([]),
                'trending_products' => $this->getTrendingProducts() ?? collect([]),
                'new_arrivals' => $this->getNewArrivals() ?? collect([]),
                'top_stores' => $this->getTopStores() ?? collect([]),
                'deals_of_the_day' => $this->getDealsOfTheDay() ?? collect([]),
            ];
        } catch (\Exception $e) {
            \Log::error('Error getting home page data: ' . $e->getMessage());
            return [
                'featured_categories' => collect([]),
                'trending_products' => collect([]),
                'new_arrivals' => collect([]),
                'top_stores' => collect([]),
                'deals_of_the_day' => collect([]),
            ];
        }
    }

    /**
     * Get featured categories (top 8 by product count).
     */
    public function getFeaturedCategories(int $limit = 8): Collection
    {
        return $this->categoryRepository->getFeaturedCategories($limit);
    }

    /**
     * Get trending products.
     */
    public function getTrendingProducts(int $limit = 12): Collection
    {
        return $this->productRepository->getTrendingProducts($limit);
    }

    /**
     * Get new arrivals (last 30 days).
     */
    public function getNewArrivals(int $limit = 12, int $days = 30): Collection
    {
        return $this->productRepository->getNewArrivals($limit, $days);
    }

    /**
     * Get top rated stores.
     */
    public function getTopStores(int $limit = 8): Collection
    {
        return $this->storeRepository->getTopStores($limit);
    }

    /**
     * Get deals of the day (products with sale price).
     */
    public function getDealsOfTheDay(int $limit = 12): Collection
    {
        return $this->productRepository->getDealsOfTheDay($limit);
    }

    /**
     * Get products with filters and pagination.
     */
    public function getProductsWithFilters(array $filters, int $perPage = 24): LengthAwarePaginator
    {
        return $this->productRepository->getProductsWithFilters($filters, $perPage);
    }

    /**
     * Get product by slug.
     */
    public function getProductBySlug(string $slug)
    {
        return $this->productRepository->getFirstBy(['slug' => $slug]);
    }

    /**
     * Get products by category slug.
     */
    public function getProductsByCategory(string $categorySlug, array $filters, int $perPage = 24): array
    {
        $category = $this->categoryRepository->getFirstBy(['slug' => $categorySlug]);
        
        if (!$category) {
            return [
                'category' => null,
                'products' => null,
            ];
        }

        $products = $this->productRepository->getProductsByCategory($category->id, $filters, $perPage);

        return [
            'category' => $category,
            'products' => $products,
        ];
    }

    /**
     * Search products globally.
     */
    public function searchProducts(string $query, int $perPage = 24): LengthAwarePaginator
    {
        return $this->productRepository->searchProducts($query, $perPage);
    }

    /**
     * Get all active stores.
     */
    public function getActiveStores(int $perPage = 24): LengthAwarePaginator
    {
        return $this->storeRepository->paginateWithFilters(['status' => 'active'], $perPage);
    }

    /**
     * Get all active categories.
     */
    public function getActiveCategories(): Collection
    {
        return $this->categoryRepository->getBy(['status' => 'active']);
    }

    /**
     * Get related products for a product.
     */
    public function getRelatedProducts(string $productId, int $limit = 8): Collection
    {
        return $this->productRepository->getRelatedProducts($productId, $limit);
    }

    /**
     * Get more products from same store.
     */
    public function getStoreProducts(string $storeId, ?string $excludeProductId = null, int $limit = 8): Collection
    {
        return $this->productRepository->getStoreProducts($storeId, $excludeProductId, $limit);
    }
}
