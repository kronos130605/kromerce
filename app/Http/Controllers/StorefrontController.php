<?php

namespace App\Http\Controllers;

use App\Helpers\TranslationHelper;
use App\Services\StorefrontService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StorefrontController extends Controller
{
    public function __construct(
        private StorefrontService $storefrontService
    ) {}

    /**
     * Display marketplace home page.
     */
    public function home(): Response
    {
        $data = $this->storefrontService->getHomePageData();

        return Inertia::render('storefront/Home', array_merge($data,
            [
                'translations' => TranslationHelper::forPreset('storefront'),
            ]
        ));
    }

    /**
     * Display product listing page.
     */
    public function products(Request $request): Response
    {
        $filters = $request->only(['search', 'category', 'store', 'min_price', 'max_price', 'sort_by', 'sort_order']);

        $products = $this->storefrontService->getProductsWithFilters($filters);
        $categories = $this->storefrontService->getActiveCategories();
        $stores = $this->storefrontService->getActiveStores(1000); // Get all for filter

        return Inertia::render('storefront/ProductListing', [
            'products' => $products,
            'categories' => $categories,
            'stores' => $stores,
            'filters' => $filters,
        ]);
    }

    /**
     * Display product detail page.
     */
    public function productDetail(string $slug): Response
    {
        $product = $this->storefrontService->getProductBySlug($slug);

        if (!$product) {
            abort(404, 'Product not found');
        }

        $product->load(['images', 'variants', 'categories', 'store']);

        $relatedProducts = $this->storefrontService->getRelatedProducts($product->id);
        $storeProducts = $this->storefrontService->getStoreProducts($product->store_id, $product->id);

        return Inertia::render('storefront/ProductDetail', [
            'product' => $product,
            'related_products' => $relatedProducts,
            'store_products' => $storeProducts,
        ]);
    }

    /**
     * Display category page.
     */
    public function category(string $slug, Request $request): Response
    {
        $filters = $request->only(['search', 'store', 'min_price', 'max_price', 'sort_by', 'sort_order']);

        $result = $this->storefrontService->getProductsByCategory($slug, $filters);

        if (!$result['category']) {
            abort(404, 'Category not found');
        }

        $stores = $this->storefrontService->getActiveStores(1000);

        return Inertia::render('storefront/CategoryProducts', [
            'category' => $result['category'],
            'products' => $result['products'],
            'stores' => $stores,
            'filters' => $filters,
        ]);
    }

    /**
     * Global search.
     */
    public function search(Request $request): Response
    {
        $query = $request->get('q', '');
        $products = $this->storefrontService->searchProducts($query);

        return Inertia::render('storefront/SearchResults', [
            'query' => $query,
            'products' => $products,
        ]);
    }

    /**
     * Display all stores.
     */
    public function stores(): Response
    {
        $stores = $this->storefrontService->getActiveStores(24);

        return Inertia::render('storefront/StoresList', [
            'stores' => $stores,
        ]);
    }
}
