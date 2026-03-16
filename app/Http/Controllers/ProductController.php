<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ProductService;
use App\Http\Requests\ProductRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
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

            return Inertia::render('Business/Index', [
                'activeTab' => 'products',
                'productsView' => 'create',
                'categories' => $categories,
            ]);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Store a newly created product.
     */
    public function store(ProductRequest $request): JsonResponse|RedirectResponse
    {
        try {
            $store = $this->validateStore();
            $user = $request->user();

            $product = $this->productService->createProductForStore(
                $store,
                $user,
                $request->validated()
            );

            if ($request->wantsJson()) {
                return $this->success($product, 'Product created successfully', 201);
            }

            return redirect()->route('products.index');

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return $this->error('Failed to create product', 500);
            }

            throw $e;
        }
    }

    /**
     * Display the specified product.
     */
    public function show(ProductRequest $request, Product $product): Response
    {
        try {
            $store = $this->validateStore();
            $product = $this->productService->getProductForStore($store, $product->id);

            if (!$product) {
                abort(404);
            }

            return Inertia::render('Business/Index', [
                'activeTab' => 'products',
                'productsView' => 'show',
                'product' => $product,
            ]);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(ProductRequest $request, Product $product): Response
    {
        try {
            $store = $this->validateStore();
            $product = $this->productService->getProductForStore($store, $product->id);
            $categories = $this->productService->getCategoriesForStore($store);

            if (!$product) {
                abort(404);
            }

            return Inertia::render('Business/Index', [
                'activeTab' => 'products',
                'productsView' => 'edit',
                'product' => $product,
                'categories' => $categories,
            ]);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Update the specified product.
     */
    public function update(ProductRequest $request, Product $product): JsonResponse|RedirectResponse
    {
        try {
            $store = $this->validateStore();

            $updated = $this->productService->updateProductForStore(
                $store,
                $product->id,
                $request->validated()
            );

            if (!$updated) {
                if ($request->wantsJson()) {
                    return $this->notFound('Product not found');
                }

                abort(404);
            }

            if ($request->wantsJson()) {
                return $this->success(null, 'Product updated successfully');
            }

            return redirect()->route('products.show', $product);

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return $this->error('Failed to update product', 500);
            }

            throw $e;
        }
    }

    /**
     * Remove the specified product.
     */
    public function destroy(ProductRequest $request, Product $product): JsonResponse|RedirectResponse
    {
        try {
            $store = $this->validateStore();

            $deleted = $this->productService->deleteProductForStore($store, $product->id);

            if (!$deleted) {
                if ($request->wantsJson()) {
                    return $this->notFound('Product not found');
                }

                abort(404);
            }

            if ($request->wantsJson()) {
                return $this->noContent('Product deleted successfully');
            }

            return redirect()->route('products.index');

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return $this->error('Failed to delete product', 500);
            }

            throw $e;
        }
    }
}
