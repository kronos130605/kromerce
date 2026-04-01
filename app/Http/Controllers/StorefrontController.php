<?php

namespace App\Http\Controllers;

use App\Helpers\TranslationHelper;
use App\Http\Resources\Storefront\ProductResource;
use App\Http\Resources\Storefront\SimpleProductResource;
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
    public function productDetail(string $productId): Response
    {
        $product = Product::findOrFail($productId);
        $product->load(['images', 'variants', 'categories', 'store']);

        // Load related products with their relationships
        $relatedProducts = $this->storefrontService->getRelatedProducts($product->id);
        $relatedProducts->load(['images', 'store']);

        // Load store products with their relationships
        $storeProducts = $this->storefrontService->getStoreProducts((string) $product->store_id, $product->id);
        $storeProducts->load(['images', 'store']);

        // Build main product resource
        $productResource = [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'description' => $product->description,
            'short_description' => $product->short_description,
            'base_price' => $product->base_price,
            'sale_price' => $product->sale_price,
            'base_currency' => $product->base_currency,
            'stock_quantity' => $product->stock_quantity,
            'status' => $product->status,
            'is_on_sale' => $product->is_on_sale,
            'condition' => $product->condition,
            'sku' => $product->sku,
            'rating' => $product->rating,
            'reviews_count' => $product->reviews_count,
            'sales_count' => $product->sales_count,
            'featured' => $product->featured,
            'is_new' => $product->is_new,
            'images' => $this->formatProductImages($product->images),
            'variants' => $product->variants->map(fn ($v) => [
                'id' => $v->id,
                'name' => $v->name,
                'sku' => $v->sku,
                'price_adjustment' => $v->price_adjustment,
                'stock_quantity' => $v->stock_quantity,
            ])->toArray(),
            'categories' => $product->categories->map(fn ($cat) => [
                'id' => $cat->id,
                'name' => $cat->name,
                'slug' => $cat->slug,
            ])->toArray(),
            'store' => $product->store ? [
                'id' => $product->store->id,
                'name' => $product->store->name,
                'slug' => $product->store->slug,
                'logo' => $product->store->logo,
            ] : null,
            'created_at' => $product->created_at?->toIso8601String(),
            'updated_at' => $product->updated_at?->toIso8601String(),
        ];

        // Build related products with full data for ProductCard
        $relatedResource = [];
        foreach ($relatedProducts as $p) {
            $relatedResource[] = [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'description' => $p->short_description ?? $p->description,
                'base_price' => $p->base_price,
                'sale_price' => $p->sale_price,
                'base_currency' => $p->base_currency,
                'stock_quantity' => $p->stock_quantity,
                'is_on_sale' => $p->is_on_sale,
                'condition' => $p->condition,
                'rating' => $p->rating,
                'reviews_count' => $p->reviews_count,
                'sales_count' => $p->sales_count,
                'is_new' => $p->is_new,
                'featured' => $p->featured,
                'images' => $this->formatProductImages($p->images),
                'store' => $p->store ? [
                    'id' => $p->store->id,
                    'name' => $p->store->name,
                    'slug' => $p->store->slug,
                ] : null,
            ];
        }

        // Build store products with full data for ProductCard
        $storeResource = [];
        foreach ($storeProducts as $p) {
            $storeResource[] = [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'description' => $p->short_description ?? $p->description,
                'base_price' => $p->base_price,
                'sale_price' => $p->sale_price,
                'base_currency' => $p->base_currency,
                'stock_quantity' => $p->stock_quantity,
                'is_on_sale' => $p->is_on_sale,
                'condition' => $p->condition,
                'rating' => $p->rating,
                'reviews_count' => $p->reviews_count,
                'sales_count' => $p->sales_count,
                'is_new' => $p->is_new,
                'featured' => $p->featured,
                'images' => $this->formatProductImages($p->images),
                'store' => $p->store ? [
                    'id' => $p->store->id,
                    'name' => $p->store->name,
                    'slug' => $p->store->slug,
                ] : null,
            ];
        }

        return Inertia::render('storefront/ProductDetail', [
            'product' => $productResource,
            'related_products' => $relatedResource,
            'store_products' => $storeResource,
            'translations' => TranslationHelper::forPreset('storefront'),
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
     * Format product images with absolute URLs.
     */
    private function formatProductImages($images): array
    {
        if (!$images || $images->isEmpty()) {
            return [];
        }

        return $images->map(fn ($img) => [
            'id' => $img->id,
            'url' => $img->url && !str_starts_with($img->url, 'http') ? asset($img->url) : $img->url,
            'thumbnail_url' => $img->thumbnail_url && !str_starts_with($img->thumbnail_url, 'http') 
                ? asset($img->thumbnail_url) 
                : $img->thumbnail_url,
            'alt_text' => $img->alt_text,
            'is_primary' => $img->is_primary,
        ])->toArray();
    }
}
