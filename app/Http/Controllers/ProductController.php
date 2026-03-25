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
            return Inertia::render('products/Index', [
                'products' => $products,
                'categories' => $categories,
                'filters' => $filters,
                'statistics' => $statistics,
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
     * 
     * @deprecated Use modal in products/Index instead
     */
    public function create(ProductRequest $request): RedirectResponse
    {
        // Redirect to products index with modal trigger
        return redirect()->route('products.index', ['modal' => 'create']);
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
     * 
     * @deprecated Use modal in products/Index instead
     */
    public function show(ProductRequest $request, Product $product): RedirectResponse
    {
        // Redirect to products index with modal trigger
        return redirect()->route('products.index', ['modal' => 'view', 'product' => $product->id]);
    }

    /**
     * Show the form for editing the specified product.
     * 
     * @deprecated Use modal in products/Index instead
     */
    public function edit(ProductRequest $request, Product $product): RedirectResponse
    {
        // Redirect to products index with modal trigger
        return redirect()->route('products.index', ['modal' => 'edit', 'product' => $product->id]);
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
