<?php

namespace App\Http\Controllers;

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

        return Inertia::render('storefront/StoreHome', array_merge([
            'store' => $store,
        ], $data));
    }

    /**
     * Display store products page.
     */
    public function products(Store $store, Request $request): Response
    {
        $filters = $request->only(['search', 'category', 'min_price', 'max_price', 'sort_by', 'sort_order']);
        $products = $this->storePageService->getStoreProducts($store->id, $filters);
        $categories = $this->storePageService->getStoreCategories($store->id);

        return Inertia::render('storefront/StoreProducts', [
            'store' => $store,
            'products' => $products,
            'categories' => $categories,
            'filters' => $filters,
        ]);
    }

    /**
     * Display store about page.
     */
    public function about(Store $store): Response
    {
        $store->load(['owner']);

        return Inertia::render('storefront/StoreAbout', [
            'store' => $store,
        ]);
    }
}
