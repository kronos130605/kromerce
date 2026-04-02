<?php

namespace App\Services;

use App\Repositories\Product\ProductRepository;
use App\Repositories\Product\ProductCategoryRepository;
use App\Repositories\Store\StoreRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class StorefrontService
{
    // Cache TTL in seconds (1 minute for home page data)
    private const HOME_CACHE_TTL = 60;

    public function __construct(
        private ProductRepository $productRepository,
        private ProductCategoryRepository $categoryRepository,
        private StoreRepository $storeRepository
    ) {}

    /**
     * Get data for marketplace home page with caching.
     */
    public function getHomePageData(): array
    {
        // DEBUG: Limpiar caché para ver datos reales
        Cache::forget('storefront.home_data');

        $data = Cache::remember('storefront.home_data', self::HOME_CACHE_TTL, function () {
            try {
                $result = [
                    'featured_categories' => $this->getFeaturedCategories(),
                    'trending_products' => $this->getTrendingProducts(),
                    'new_arrivals' => $this->getNewArrivals(),
                    'top_stores' => $this->getTopStores(),
                    'deals_of_the_day' => $this->getDealsOfTheDay(),
                ];

                return $result;
            } catch (\Exception $e) {
                Log::error('Error getting home page data: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                ]);

                return [
                    'featured_categories' => collect(),
                    'trending_products' => collect(),
                    'new_arrivals' => collect(),
                    'top_stores' => collect(),
                    'deals_of_the_day' => collect(),
                ];
            }
        });

        return $data;
    }

    /**
     * Clear home page cache.
     */
    public function clearHomeCache(): void
    {
        Cache::forget('storefront.home_data');
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
