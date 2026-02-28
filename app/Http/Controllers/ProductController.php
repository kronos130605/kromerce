<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductTag;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\BusinessCurrencyConfig;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    /**
     * Display the products page.
     */
    public function index(Request $request): Response
    {
        $tenant = $request->user()->tenant;
        
        $query = Product::where('tenant_id', $tenant->id)
            ->with(['categories', 'primaryImage', 'variants'])
            ->withCount('variants');

        // Filters
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->search}%")
                  ->orWhere('description', 'LIKE', "%{$request->search}%")
                  ->orWhere('sku', 'LIKE', "%{$request->search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'in_stock') {
                $query->inStock();
            } elseif ($request->stock_status === 'low_stock') {
                $query->where('manage_stock', true)
                    ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
                    ->where('stock_quantity', '>', 0);
            } elseif ($request->stock_status === 'out_of_stock') {
                $query->where('manage_stock', true)
                    ->where('stock_quantity', '<=', 0);
            }
        }

        if ($request->filled('featured')) {
            $query->where('featured', $request->boolean('featured'));
        }

        $products = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 12));

        // Get currency config for price display
        $currencyConfig = $tenant->currencyConfig;
        $selectedCurrency = $request->get('currency', $currencyConfig->default_currency);
        $supportedCurrencies = $currencyConfig->getSupportedCurrenciesWithRates();

        // Calculate prices for display
        $products->getCollection()->transform(function ($product) use ($selectedCurrency, $supportedCurrencies) {
            $calculatedPrices = $product->getCalculatedPrices();
            $product->display_price = $calculatedPrices[$selectedCurrency] ?? null;
            $product->supported_currencies = $supportedCurrencies;
            return $product;
        });

        return Inertia::render('Products/Index', [
            'products' => $products,
            'filters' => $request->only(['search', 'status', 'category', 'stock_status', 'featured', 'currency']),
            'categories' => ProductCategory::where('tenant_id', $tenant->id)
                ->active()
                ->orderBy('name')
                ->get(),
            'supportedCurrencies' => $supportedCurrencies,
            'selectedCurrency' => $selectedCurrency,
        ]);
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(Request $request): Response
    {
        $tenant = $request->user()->tenant;
        
        $currencyConfig = $tenant->currencyConfig;
        $supportedCurrencies = $currencyConfig->getSupportedCurrenciesWithRates();

        return Inertia::render('Products/Create', [
            'categories' => ProductCategory::where('tenant_id', $tenant->id)
                ->active()
                ->orderBy('name')
                ->get(),
            'tags' => ProductTag::where('tenant_id', $tenant->id)
                ->orderBy('name')
                ->get(),
            'currencyConfig' => $currencyConfig,
            'supportedCurrencies' => $supportedCurrencies,
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
            'base_sale_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'is_on_sale' => 'boolean',
            'sale_type' => 'nullable|in:fixed,percentage',
            'sale_discount' => 'nullable|numeric|min:0|max:100',
            'sale_start_date' => 'nullable|date',
            'sale_end_date' => 'nullable|date|after_or_equal:sale_start_date',
            'track_cost' => 'boolean',
            'show_cost_to_customer' => 'boolean',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'barcode' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive,draft',
            'visibility' => 'required|in:public,private,hidden',
            'featured' => 'boolean',
            'downloadable' => 'boolean',
            'virtual' => 'boolean',
            'manage_stock' => 'boolean',
            'stock_quantity' => 'nullable|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'shipping_class' => 'nullable|string|max:50',
            'free_shipping' => 'boolean',
            'tax_class' => 'nullable|string|max:50',
            'tax_status' => 'required|in:taxable,exempt',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:product_categories,id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:product_tags,id',
            'images' => 'nullable|array',
            'images.*.url' => 'required|string',
            'images.*.alt' => 'nullable|string',
            'images.*.order' => 'nullable|integer',
            'images.*.is_primary' => 'boolean',
        ]);

        $tenant = $request->user()->tenant;
        $currencyConfig = $tenant->currencyConfig;

        $product = Product::create([
            'tenant_id' => $tenant->id,
            'name' => $validated['name'],
            'description' => $validated['description'],
            'short_description' => $validated['short_description'],
            'base_currency' => $currencyConfig->default_currency,
            'base_price' => $validated['base_price'],
            'base_sale_price' => $validated['base_sale_price'],
            'cost_price' => $validated['cost_price'],
            'is_on_sale' => $validated['is_on_sale'],
            'sale_type' => $validated['sale_type'],
            'sale_discount' => $validated['sale_discount'],
            'sale_start_date' => $validated['sale_start_date'],
            'sale_end_date' => $validated['sale_end_date'],
            'track_cost' => $validated['track_cost'],
            'show_cost_to_customer' => $validated['show_cost_to_customer'],
            'sku' => $validated['sku'],
            'barcode' => $validated['barcode'],
            'status' => $validated['status'],
            'visibility' => $validated['visibility'],
            'featured' => $validated['featured'],
            'downloadable' => $validated['downloadable'],
            'virtual' => $validated['virtual'],
            'manage_stock' => $validated['manage_stock'],
            'stock_quantity' => $validated['stock_quantity'] ?? 0,
            'low_stock_threshold' => $validated['low_stock_threshold'] ?? 0,
            'weight' => $validated['weight'],
            'length' => $validated['length'],
            'width' => $validated['width'],
            'height' => $validated['height'],
            'shipping_class' => $validated['shipping_class'],
            'free_shipping' => $validated['free_shipping'],
            'tax_class' => $validated['tax_class'],
            'tax_status' => $validated['tax_status'],
            'created_by' => $request->user()->id,
        ]);

        // Attach categories
        if (!empty($validated['category_ids'])) {
            $product->categories()->attach($validated['category_ids']);
        }

        // Attach tags
        if (!empty($validated['tag_ids'])) {
            $product->tags()->attach($validated['tag_ids']);
        }

        // Add images
        if (!empty($validated['images'])) {
            foreach ($validated['images'] as $imageData) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'url' => $imageData['url'],
                    'alt' => $imageData['alt'] ?? null,
                    'title' => $imageData['title'] ?? null,
                    'order' => $imageData['order'] ?? 0,
                    'is_primary' => $imageData['is_primary'] ?? false,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully',
            'product' => $product->load(['categories', 'tags', 'images']),
        ]);
    }

    /**
     * Display the specified product.
     */
    public function show(Request $request, Product $product): Response
    {
        // Ensure user can only view their own products
        if ($product->tenant_id !== $request->user()->tenant_id) {
            abort(403);
        }

        $tenant = $request->user()->tenant;
        $currencyConfig = $tenant->currencyConfig;
        $selectedCurrency = $request->get('currency', $currencyConfig->default_currency);
        $supportedCurrencies = $currencyConfig->getSupportedCurrenciesWithRates();

        $product->load(['categories', 'tags', 'images' => function ($query) {
            $query->orderBy('order');
        }, 'variants', 'priceHistory']);

        $calculatedPrices = $product->getCalculatedPrices();

        return Inertia::render('Products/Show', [
            'product' => $product,
            'calculatedPrices' => $calculatedPrices,
            'selectedCurrency' => $selectedCurrency,
            'supportedCurrencies' => $supportedCurrencies,
        ]);
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Request $request, Product $product): Response
    {
        // Ensure user can only edit their own products
        if ($product->tenant_id !== $request->user()->tenant_id) {
            abort(403);
        }

        $tenant = $request->user()->tenant;
        
        $currencyConfig = $tenant->currencyConfig;
        $supportedCurrencies = $currencyConfig->getSupportedCurrenciesWithRates();

        $product->load(['categories', 'tags', 'images' => function ($query) {
            $query->orderBy('order');
        }, 'variants']);

        return Inertia::render('Products/Edit', [
            'product' => $product,
            'categories' => ProductCategory::where('tenant_id', $tenant->id)
                ->active()
                ->orderBy('name')
                ->get(),
            'tags' => ProductTag::where('tenant_id', $tenant->id)
                ->orderBy('name')
                ->get(),
            'currencyConfig' => $currencyConfig,
            'supportedCurrencies' => $supportedCurrencies,
        ]);
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        // Ensure user can only update their own products
        if ($product->tenant_id !== $request->user()->tenant_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'base_price' => 'required|numeric|min:0',
            'base_sale_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'is_on_sale' => 'boolean',
            'sale_type' => 'nullable|in:fixed,percentage',
            'sale_discount' => 'nullable|numeric|min:0|max:100',
            'sale_start_date' => 'nullable|date',
            'sale_end_date' => 'nullable|date|after_or_equal:sale_start_date',
            'track_cost' => 'boolean',
            'show_cost_to_customer' => 'boolean',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive,draft',
            'visibility' => 'required|in:public,private,hidden',
            'featured' => 'boolean',
            'downloadable' => 'boolean',
            'virtual' => 'boolean',
            'manage_stock' => 'boolean',
            'stock_quantity' => 'nullable|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'shipping_class' => 'nullable|string|max:50',
            'free_shipping' => 'boolean',
            'tax_class' => 'nullable|string|max:50',
            'tax_status' => 'required|in:taxable,exempt',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:product_categories,id',
            'tag_ids' => 'nullable|array',
            'tag_ids.*' => 'exists:product_tags,id',
        ]);

        $product->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'short_description' => $validated['short_description'],
            'base_price' => $validated['base_price'],
            'base_sale_price' => $validated['base_sale_price'],
            'cost_price' => $validated['cost_price'],
            'is_on_sale' => $validated['is_on_sale'],
            'sale_type' => $validated['sale_type'],
            'sale_discount' => $validated['sale_discount'],
            'sale_start_date' => $validated['sale_start_date'],
            'sale_end_date' => $validated['sale_end_date'],
            'track_cost' => $validated['track_cost'],
            'show_cost_to_customer' => $validated['show_cost_to_customer'],
            'sku' => $validated['sku'],
            'barcode' => $validated['barcode'],
            'status' => $validated['status'],
            'visibility' => $validated['visibility'],
            'featured' => $validated['featured'],
            'downloadable' => $validated['downloadable'],
            'virtual' => $validated['virtual'],
            'manage_stock' => $validated['manage_stock'],
            'stock_quantity' => $validated['stock_quantity'] ?? 0,
            'low_stock_threshold' => $validated['low_stock_threshold'] ?? 0,
            'weight' => $validated['weight'],
            'length' => $validated['length'],
            'width' => $validated['width'],
            'height' => $validated['height'],
            'shipping_class' => $validated['shipping_class'],
            'free_shipping' => $validated['free_shipping'],
            'tax_class' => $validated['tax_class'],
            'tax_status' => $validated['tax_status'],
            'updated_by' => $request->user()->id,
        ]);

        // Sync categories
        $product->categories()->sync($validated['category_ids'] ?? []);

        // Sync tags
        $product->tags()->sync($validated['tag_ids'] ?? []);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'product' => $product->load(['categories', 'tags']),
        ]);
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Request $request, Product $product): JsonResponse
    {
        // Ensure user can only delete their own products
        if ($product->tenant_id !== $request->user()->tenant_id) {
            abort(403);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully',
        ]);
    }

    /**
     * Get product prices in different currencies.
     */
    public function getPrices(Request $request, Product $product): JsonResponse
    {
        // Ensure user can only view their own products
        if ($product->tenant_id !== $request->user()->tenant_id) {
            abort(403);
        }

        $calculatedPrices = $product->getCalculatedPrices();

        return response()->json([
            'prices' => $calculatedPrices,
        ]);
    }

    /**
     * Duplicate a product.
     */
    public function duplicate(Request $request, Product $product): JsonResponse
    {
        // Ensure user can only duplicate their own products
        if ($product->tenant_id !== $request->user()->tenant_id) {
            abort(403);
        }

        $newProduct = $product->replicate();
        $newProduct->name = $product->name . ' (Copy)';
        $newProduct->slug = null; // Will be regenerated
        $newProduct->sku = null; // Will be regenerated
        $newProduct->created_by = $request->user()->id;
        $newProduct->updated_by = null;
        $newProduct->save();

        // Copy categories
        $newProduct->categories()->attach($product->categories->pluck('id'));

        // Copy tags
        $newProduct->tags()->attach($product->tags->pluck('id'));

        // Copy images
        foreach ($product->images as $image) {
            ProductImage::create([
                'product_id' => $newProduct->id,
                'url' => $image->url,
                'alt' => $image->alt,
                'title' => $image->title,
                'order' => $image->order,
                'is_primary' => false, // Reset primary image
                'metadata' => $image->metadata,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product duplicated successfully',
            'product' => $newProduct->load(['categories', 'tags', 'images']),
        ]);
    }
}
