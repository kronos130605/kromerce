<?php

namespace App\Http\Controllers\Storefront;

use App\Helpers\TranslationHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Storefront\CategoryCardResource;
use App\Http\Resources\Storefront\ProductCardResource;
use App\Http\Resources\Storefront\ProductResource;
use App\Http\Resources\Storefront\StoreCardResource;
use App\Models\Product;
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

        return Inertia::render('storefront/Home', [
            'featured_categories' => CategoryCardResource::collection($data['featured_categories'])->resolve(),
            'trending_products' => ProductCardResource::collection($data['trending_products'])->resolve(),
            'new_arrivals' => ProductCardResource::collection($data['new_arrivals'])->resolve(),
            'top_stores' => StoreCardResource::collection($data['top_stores'])->resolve(),
            'deals_of_the_day' => ProductCardResource::collection($data['deals_of_the_day'])->resolve(),
            'translations' => TranslationHelper::forPreset('storefront'),
        ]);
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
    public function productDetail(string $productId, Request $request): Response
    {
        $product = Product::findOrFail($productId);
        $product->load(['images', 'variants', 'categories', 'store']);

        // Load related and store products with optimized relationships
        $relatedProducts = $this->storefrontService->getRelatedProducts($product->id);
        $relatedProducts->load(['images', 'store']);

        $storeProducts = $this->storefrontService->getStoreProducts((string) $product->store_id, $product->id);
        $storeProducts->load(['images', 'store']);

        // Determine breadcrumb context based on referrer
        $referer = $request->headers->get('referer', '');
        $breadcrumbContext = 'default';
        $breadcrumbStore = null;

        if (str_contains($referer, '/marketplace/stores/')) {
            $breadcrumbContext = 'store';
            // Extract store slug from referer if possible
            if (preg_match('/\/marketplace\/stores\/([^\/]+)/', $referer, $matches)) {
                $storeSlug = $matches[1];
                if ($product->store && $product->store->slug === $storeSlug) {
                    $breadcrumbStore = $product->store;
                }
            }
            if (!$breadcrumbStore && $product->store) {
                $breadcrumbStore = $product->store;
            }
        } elseif (str_contains($referer, '/marketplace/products')) {
            $breadcrumbContext = 'products';
        }

        return Inertia::render('storefront/ProductDetail', [
            'product' => (new ProductResource($product))->resolve(),
            'related_products' => ProductCardResource::collection($relatedProducts)->resolve(),
            'store_products' => ProductCardResource::collection($storeProducts)->resolve(),
            'translations' => TranslationHelper::forPreset('storefront'),
            'breadcrumb_context' => $breadcrumbContext,
            'breadcrumb_store' => $breadcrumbStore ? (new StoreCardResource($breadcrumbStore))->resolve() : null,
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
}
