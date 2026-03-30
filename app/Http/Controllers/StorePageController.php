<?php

namespace App\Http\Controllers;

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
    public function home(string $slug): Response
    {
        $store = $this->storePageService->getStoreBySlug($slug);

        if (!$store) {
            abort(404, 'Store not found');
        }

        $store->load(['owner']);
        $data = $this->storePageService->getStoreHomeData($store->id);

        return Inertia::render('storefront/StoreHome', array_merge([
            'store' => $store,
        ], $data));
    }

    /**
     * Display store products page.
     */
    public function products(string $slug, Request $request): Response
    {
        $store = $this->storePageService->getStoreBySlug($slug);

        if (!$store) {
            abort(404, 'Store not found');
        }

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
    public function about(string $slug): Response
    {
        $store = $this->storePageService->getStoreBySlug($slug);

        if (!$store) {
            abort(404, 'Store not found');
        }

        $store->load(['owner']);

        return Inertia::render('storefront/StoreAbout', [
            'store' => $store,
        ]);
    }
}
