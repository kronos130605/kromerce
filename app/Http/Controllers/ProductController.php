<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use App\Http\Requests\ProductRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {
        // Apply business role middleware to all product methods
        $this->middleware('role:business_owner');
    }

    /**
     * Display products page.
     */
    public function index(Request $request): Response|JsonResponse
    {
        try {
            $store = $this->validateStore();

            // Get products data using the service
            $filters = $request->all();
            $products = $this->productService->getProductsForStore($store, $filters);
            $categories = $this->productService->getCategoriesForStore($store);
            $statistics = $this->productService->getStatisticsForStore($store);

            // Return products page with SPA structure
            return Inertia::render('Business/Index', [
                'activeTab' => 'products',
                'products' => $products,
                'categories' => $categories,
                'filters' => $filters,
                'statistics' => $statistics,
                'dashboard_data' => [
                    'totalProducts' => $statistics['total_products'] ?? 0,
                    'activeProducts' => $statistics['active_products'] ?? 0,
                    'lowStock' => $statistics['low_stock'] ?? 0,
                    'outOfStock' => $statistics['out_of_stock'] ?? 0,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('ProductController::index - ERROR', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'path' => $request->path(),
            ]);

            throw $e;
        }
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(ProductRequest $request): Response
    {
        try {
            $store = $this->validateStore();
            $categories = $this->productService->getCategoriesForStore($store);

            return Inertia::render('modules/products/Products/Create', [
                'categories' => $categories,
            ]);

        } catch (\Exception $e) {
            return Inertia::render('modules/products/Products/Error', [
                'error' => 'Failed to load product creation form',
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Store a newly created product.
     */
    public function store(ProductRequest $request): JsonResponse
    {
        try {
            $store = $this->validateStore();
            $user = $request->user();

            $product = $this->productService->createProductForStore(
                $store,
                $user,
                $request->validated()
            );

            return $this->success($product, 'Product created successfully', 201);

        } catch (\Exception $e) {
            return $this->error('Failed to create product', 500);
        }
    }

    /**
     * Display the specified product.
     */
    public function show(ProductRequest $request, int $id): Response
    {
        try {
            $store = $this->validateStore();
            $product = $this->productService->getProductForStore($store, $id);

            if (!$product) {
                return Inertia::render('modules/products/Products/Error', [
                    'error' => 'Product not found',
                    'message' => 'The requested product could not be found.',
                ]);
            }

            return Inertia::render('modules/products/Products/Show', [
                'product' => $product,
            ]);

        } catch (\Exception $e) {
            return Inertia::render('modules/products/Products/Error', [
                'error' => 'Failed to load product',
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(ProductRequest $request, int $id): Response
    {
        try {
            $store = $this->validateStore();
            $product = $this->productService->getProductForStore($store, $id);
            $categories = $this->productService->getCategoriesForStore($store);

            if (!$product) {
                return Inertia::render('modules/products/Products/Error', [
                    'error' => 'Product not found',
                    'message' => 'The requested product could not be found.',
                ]);
            }

            return Inertia::render('modules/products/Products/Edit', [
                'product' => $product,
                'categories' => $categories,
            ]);

        } catch (\Exception $e) {
            return Inertia::render('modules/products/Products/Error', [
                'error' => 'Failed to load product edit form',
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update the specified product.
     */
    public function update(ProductRequest $request, int $id): JsonResponse
    {
        try {
            $store = $this->validateStore();

            $updated = $this->productService->updateProductForStore(
                $store,
                $id,
                $request->validated()
            );

            if (!$updated) {
                return $this->notFound('Product not found');
            }

            return $this->success(null, 'Product updated successfully');

        } catch (\Exception $e) {
            return $this->error('Failed to update product', 500);
        }
    }

    /**
     * Remove the specified product.
     */
    public function destroy(ProductRequest $request, int $id): JsonResponse
    {
        try {
            $store = $this->validateStore();

            $deleted = $this->productService->deleteProductForStore($store, $id);

            if (!$deleted) {
                return $this->notFound('Product not found');
            }

            return $this->noContent('Product deleted successfully');

        } catch (\Exception $e) {
            return $this->error('Failed to delete product', 500);
        }
    }
}
