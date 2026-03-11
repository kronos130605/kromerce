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
    ) {}

    /**
     * Display products page.
     */
    public function index(Request $request): Response|JsonResponse
    {
        try {
            $tenant = $this->validateTenant();

            // Get products data using the service
            $filters = $request->all();
            $products = $this->productService->getProductsForTenant($tenant, $filters);
            $categories = $this->productService->getCategoriesForTenant($tenant);
            $statistics = $this->productService->getStatisticsForTenant($tenant);

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
            Log::error('ProductController::index - Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return $this->error('Failed to load products', 500);
        }
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(ProductRequest $request): Response
    {
        try {
            $tenant = $this->validateTenant();
            $categories = $this->productService->getCategoriesForTenant($tenant);

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
            $tenant = $this->validateTenant();
            $user = $request->user();

            $product = $this->productService->createProductForTenant(
                $tenant,
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
            $tenant = $this->validateTenant();
            $product = $this->productService->getProductForTenant($tenant, $id);

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
            $tenant = $this->validateTenant();
            $product = $this->productService->getProductForTenant($tenant, $id);
            $categories = $this->productService->getCategoriesForTenant($tenant);

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
            $tenant = $this->validateTenant();

            $updated = $this->productService->updateProductForTenant(
                $tenant,
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
            $tenant = $this->validateTenant();

            $deleted = $this->productService->deleteProductForTenant($tenant, $id);

            if (!$deleted) {
                return $this->notFound('Product not found');
            }

            return $this->noContent('Product deleted successfully');

        } catch (\Exception $e) {
            return $this->error('Failed to delete product', 500);
        }
    }
}
