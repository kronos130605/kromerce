<?php

namespace App\Http\Controllers\Storefront;

use App\Helpers\TranslationHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Storefront\CategoryCardResource;
use App\Http\Resources\Storefront\ProductCardResource;
use App\Http\Resources\Storefront\StoreCardResource;
use App\Models\Store;
use App\Services\StorePageService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StorePageController extends Controller
{
    public function __construct(
        private StorePageService $storePageService
    ) {}

    /**
     * Display store home page.
     */
    public function home(Store $store): Response
    {
        $store->load(['owner']);
        $data = $this->storePageService->getStoreHomeData($store->id);

        return Inertia::render('storefront/StoreHome', [
            'store' => (new StoreCardResource($store))->resolve(),
            'featured_products' => ProductCardResource::collection($data['featured_products'])->resolve(),
            'all_products' => ProductCardResource::collection($data['all_products'])->resolve(),
            'stats' => $data['stats'],
            'translations' => TranslationHelper::forPreset('storefront'),
        ]);
    }

    /**
     * Display store products page.
     */
    public function products(Store $store, Request $request): Response
    {
        $store->load(['owner']);
        $filters = $request->only(['search', 'category', 'min_price', 'max_price', 'sort_by', 'sort_order']);
        $products = $this->storePageService->getStoreProducts($store->id, $filters);
        $categories = $this->storePageService->getStoreCategories($store->id);

        // Transform products while preserving pagination structure
        $products->setCollection(
            collect(ProductCardResource::collection($products->getCollection())->resolve())
        );

        return Inertia::render('storefront/StoreProducts', [
            'store' => (new StoreCardResource($store))->resolve(),
            'products' => $products,
            'categories' => CategoryCardResource::collection($categories)->resolve(),
            'filters' => $filters,
            'translations' => TranslationHelper::forPreset('storefront'),
        ]);
    }

    /**
     * Display store about page.
     */
    public function about(Store $store): Response
    {
        $store->load(['owner']);

        return Inertia::render('storefront/StoreAbout', [
            'store' => (new StoreCardResource($store))->resolve(),
            'translations' => TranslationHelper::forPreset('storefront'),
        ]);
    }
}
