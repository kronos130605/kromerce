<?php

namespace App\Http\Controllers;

use App\Services\ProductPricingService;
use App\Repositories\ProductRepository;
use App\Repositories\ProductCategoryRepository;
use App\Repositories\ProductTagRepository;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductCategory;
use App\Models\ProductTag;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use function tenancy;

class ProductController extends Controller
{
    private ProductPricingService $pricingService;
    private ProductRepository $productRepo;
    private ProductCategoryRepository $categoryRepo;
    private ProductTagRepository $tagRepo;

    public function __construct(
        ProductPricingService $pricingService,
        ProductRepository $productRepo,
        ProductCategoryRepository $categoryRepo,
        ProductTagRepository $tagRepo
    ) {
        $this->pricingService = $pricingService;
        $this->productRepo = $productRepo;
        $this->categoryRepo = $categoryRepo;
        $this->tagRepo = $tagRepo;
    }

    /**
     * Display the products page.
     */
    public function index(Request $request): Response|JsonResponse
    {
        // Get tenant from tenancy context instead of user
        $tenant = tenancy()->initialized ? tenant() : null;

        if (!$tenant) {
            Log::error('ProductController::index - No tenant context found');
            return response()->json(['error' => 'No tenant context found'], 403);
        }

        $filters = $request->only(['category_id', 'is_active', 'is_on_sale', 'min_price', 'max_price', 'search']);

        try {
            $products = $this->productRepo->paginateForTenant($tenant->id, $filters);
        } catch (\Exception $e) {
            Log::error('ProductController::index - Repository error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Repository error: ' . $e->getMessage()], 500);
        }

        try {
            $categories = $this->categoryRepo->getForTenant($tenant->id);
        } catch (\Exception $e) {
            Log::error('ProductController::index - Category repository error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $categories = collect([]);
        }

        try {
            $statistics = $this->productRepo->getStatistics($tenant->id);
        } catch (\Exception $e) {
            Log::error('ProductController::index - Statistics error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $statistics = [];
        }

        return Inertia::render('modules/products/Products/Index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $filters,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(Request $request): Response|JsonResponse
    {
        // Get tenant from tenancy context instead of user
        $tenant = tenancy()->initialized ? tenant() : null;

        if (!$tenant) {
            return response()->json(['error' => 'No tenant context found'], 403);
        }

        return Inertia::render('modules/products/Products/Create', [
            'categories' => $this->categoryRepo->getForTenant($tenant->id),
            'tags' => $this->tagRepo->getForTenant($tenant->id),
        ]);
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'base_price' => 'required|numeric|min:0',
            'base_currency' => 'required|string|size:3',
            'base_sale_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'track_cost' => 'boolean',
            'category_id' => 'nullable|exists:product_categories,id',
            'tags' => 'array',
            'tags.*' => 'exists:product_tags,id',
            'manage_stock' => 'boolean',
            'stock_quantity' => 'nullable|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'is_on_sale' => 'boolean',
            'sale_type' => 'nullable|in:fixed,percentage',
            'sale_amount' => 'nullable|numeric|min:0',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|array',
            'meta_keywords.*' => 'string|max:100',
            'shipping_class' => 'nullable|string|max:100',
            'free_shipping' => 'boolean',
            'tax_class' => 'nullable|string|max:100',
            'taxable' => 'boolean',
        ]);

        $tenant = $request->user()->tenant;

        $productData = array_merge($validated, [
            'tenant_id' => $tenant->id,
            'user_id' => $request->user()->id,
        ]);

        $product = $this->productRepo->create($productData);

        // Attach categories and tags
        if (isset($validated['category_id'])) {
            $product->categories()->attach($validated['category_id']);
        }

        if (isset($validated['tags'])) {
            $product->tags()->attach($validated['tags']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'product' => $product,
        ]);
    }

    /**
     * Display the specified product.
     */
    public function show(Request $request, Product $product): Response
    {
        $this->authorize('view', $product);

        $product = $this->productRepo->getWithRelationships($product->tenant_id, [
            'category',
            'tags',
            'images' => function ($query) {
                $query->orderBy('order');
            },
            'variants' => function ($query) {
                $query->where('is_active', true);
            },
            'priceHistory' => function ($query) {
                $query->latest()->limit(10);
            },
        ])->find($product->id);

        $calculatedPrices = $this->pricingService->calculateProductPrices($product);

        return Inertia::render('modules/products/Products/Show', [
            'product' => $product,
            'calculatedPrices' => $calculatedPrices,
        ]);
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Request $request, Product $product): Response
    {
        $this->authorize('update', $product);

        $tenant = $request->user()->tenant;

        return Inertia::render('modules/products/Products/Edit', [
            'product' => $product->load(['categories', 'tags', 'images']),
            'categories' => $this->categoryRepo->getForTenant($tenant->id),
            'tags' => $this->tagRepo->getForTenant($tenant->id),
        ]);
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        $this->authorize('update', $product);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_price' => 'required|numeric|min:0',
            'base_currency' => 'required|string|size:3',
            'base_sale_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'track_cost' => 'boolean',
            'category_id' => 'nullable|exists:product_categories,id',
            'tags' => 'array',
            'tags.*' => 'exists:product_tags,id',
            'manage_stock' => 'boolean',
            'stock_quantity' => 'nullable|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'is_on_sale' => 'boolean',
            'sale_type' => 'nullable|in:fixed,percentage',
            'sale_amount' => 'nullable|numeric|min:0',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|array',
            'meta_keywords.*' => 'string|max:100',
            'shipping_class' => 'nullable|string|max:100',
            'free_shipping' => 'boolean',
            'tax_class' => 'nullable|string|max:100',
            'taxable' => 'boolean',
        ]);

        $product = $this->productRepo->update($product->id, $validated);

        // Sync categories and tags
        if (isset($validated['category_id'])) {
            $product->categories()->sync([$validated['category_id']]);
        } else {
            $product->categories()->detach();
        }

        if (isset($validated['tags'])) {
            $product->tags()->sync($validated['tags']);
        } else {
            $product->tags()->detach();
        }

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'product' => $product,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Product $product): JsonResponse
    {
        $this->authorize('delete', $product);

        $this->productRepo->delete($product->id);

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully',
        ]);
    }

    /**
     * Duplicate a product.
     */
    public function duplicate(Request $request, Product $product): JsonResponse
    {
        $this->authorize('view', $product);

        $overrides = [
            'name' => $product->name . ' (Copy)',
            'user_id' => $request->user()->id,
        ];

        $newProduct = $this->productRepo->duplicate($product->id, $overrides);

        // Copy categories and tags
        $newProduct->categories()->attach($product->categories->pluck('id'));
        $newProduct->tags()->attach($product->tags->pluck('id'));

        return response()->json([
            'success' => true,
            'message' => 'Product duplicated successfully',
            'product' => $newProduct,
        ]);
    }

    /**
     * Get calculated prices for a product.
     */
    public function getPrices(Request $request, Product $product): JsonResponse
    {
        $this->authorize('view', $product);

        $targetCurrency = $request->get('currency');
        $prices = $this->pricingService->calculateProductPrices($product, $targetCurrency);

        return response()->json([
            'prices' => $prices,
        ]);
    }

    /**
     * Search products.
     */
    public function search(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'query' => 'required|string|min:2',
            'filters' => 'array',
        ]);

        $tenant = $request->user()->tenant;
        $products = $this->productRepo->search($tenant->id, $validated['query'], $validated['filters'] ?? []);

        return response()->json([
            'products' => $products,
        ]);
    }
}
