<?php

namespace App\Services;

use App\Repositories\Store\StoreRepository;
use App\Repositories\Product\ProductRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class StorePageService
{
    public function __construct(
        private StoreRepository $storeRepository,
        private ProductRepository $productRepository
    ) {}

    /**
     * Get store by slug.
     */
    public function getStoreBySlug(string $slug)
    {
        return $this->storeRepository->getFirstBy(['slug' => $slug]);
    }

    /**
     * Get store home page data.
     */
    public function getStoreHomeData(string $storeId): array
    {
        $featuredProducts = $this->productRepository->getBy([
            'store_id' => $storeId,
            'status' => 'active',
            'featured' => true
        ]);

        $allProducts = $this->productRepository->paginateWithFilters([
            'store_id' => $storeId,
            'status' => 'active'
        ], 24);

        $stats = [
            'total_products' => $this->productRepository->count(['store_id' => $storeId, 'status' => 'active']),
            'rating' => 4.5, // TODO: Calculate from reviews
            'total_reviews' => 0, // TODO: Count reviews
            'followers' => 0, // TODO: Implement followers
        ];

        return [
            'featured_products' => $featuredProducts,
            'all_products' => $allProducts,
            'stats' => $stats,
        ];
    }

    /**
     * Get store products with filters.
     */
    public function getStoreProducts(string $storeId, array $filters): LengthAwarePaginator
    {
        $filters['store_id'] = $storeId;
        $filters['status'] = 'active';
        
        return $this->productRepository->getProductsWithFilters($filters, 24);
    }

    /**
     * Get store categories.
     */
    public function getStoreCategories(string $storeId): Collection
    {
        // Get unique categories from store products
        $products = $this->productRepository->getBy([
            'store_id' => $storeId,
            'status' => 'active'
        ]);

        return $products->load('categories')
            ->pluck('categories')
            ->flatten()
            ->unique('id')
            ->values();
    }
}
