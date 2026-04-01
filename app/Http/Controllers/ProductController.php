<?php

namespace App\Http\Controllers;

use App\Helpers\TranslationHelper;
use App\Models\Product;
use App\Services\ProductService;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductImageRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Laravel\Facades\Image;
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
            $categories = $this->productService->getCategories();
            $statistics = $this->productService->getStatisticsForStore($store);

            // Return products page with SPA structure
            return Inertia::render('products/Index', [
                'products' => $products,
                'categories' => $categories,
                'filters' => $filters,
                'statistics' => $statistics,
                'translations' => TranslationHelper::forPreset('products'),
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

            return redirect()->route('products.index')->with('product_id', $product->id);

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
                abort(404);
            }

            // Inertia expects a redirect response, not JSON
            return redirect()->route('products.index')->with('success', 'Product updated successfully');

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
    public function destroy(Request $request, Product $product): JsonResponse
    {
        try {
            $store = $this->validateStore();

            // Verify product belongs to the store
            if ($product->store_id !== $store->id) {
                return $this->notFound('Product not found');
            }

            // Delete all physical image files first
            $directory = 'products/' . $product->id;
            \Storage::disk('public')->deleteDirectory($directory);

            // Delete product (this will cascade delete image records via FK)
            $deleted = $this->productService->deleteProductForStore($store, $product->id);

            if (!$deleted) {
                return $this->notFound('Product not found');
            }

            return $this->noContent('Product deleted successfully');

        } catch (\Exception $e) {
            Log::error('ProductController::destroy - ERROR', [
                'error' => $e->getMessage(),
                'product_id' => $product->id ?? null,
                'user_id' => auth()->id(),
            ]);

            return $this->error('Failed to delete product: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Upload image for a product with thumbnail generation.
     */
    public function uploadImage(ProductImageRequest $request, Product $product): JsonResponse
    {
        try {
            $store = $this->validateStore();

            // Verify product belongs to the store
            if ($product->store_id !== $store->id) {
                return $this->error('Product not found', 404);
            }

            $file = $request->file('image');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $directory = 'products/' . $product->id;

            // Store original image
            $originalPath = $file->storeAs($directory, $filename, 'public');

            // Create thumbnail (300x300, cropped)
            $thumbnailFilename = 'thumb_' . $filename;
            $thumbnailPath = storage_path('app/public/' . $directory . '/' . $thumbnailFilename);

            $image = Image::read($file->getRealPath());
            $image->cover(300, 300);
            $image->save($thumbnailPath, quality: 80);

            // Create medium size (800x800, fitted)
            $mediumFilename = 'medium_' . $filename;
            $mediumPath = storage_path('app/public/' . $directory . '/' . $mediumFilename);

            $mediumImage = Image::read($file->getRealPath());
            $mediumImage->scaleDown(800, 800);
            $mediumImage->save($mediumPath, quality: 85);

            // Parse values from request
            $isPrimary = $request->booleanIsPrimary();
            $order = $request->integerOrder($product->images()->count());

            // Create image record with relative paths (not full URLs)
            $imageRecord = $product->images()->create([
                'url' => 'storage/' . $originalPath,
                'alt' => $request->input('alt', $product->name),
                'title' => $request->input('title'),
                'is_primary' => $isPrimary,
                'order' => $order,
                'metadata' => [
                    'thumbnail' => 'storage/' . $directory . '/' . $thumbnailFilename,
                    'medium' => 'storage/' . $directory . '/' . $mediumFilename,
                    'sizes' => [
                        'original' => $originalPath,
                        'thumbnail' => $directory . '/' . $thumbnailFilename,
                        'medium' => $directory . '/' . $mediumFilename,
                    ],
                ],
            ]);

            // If this is the first image or is_primary is true, set as primary
            if ($isPrimary || $product->images()->count() === 1) {
                $product->images()->where('id', '!=', $imageRecord->id)->update(['is_primary' => false]);
                $imageRecord->update(['is_primary' => true]);
            }

            return $this->success([
                'id' => $imageRecord->id,
                'url' => asset($imageRecord->url),
                'full_url' => asset($imageRecord->url),
                'thumbnail_url' => asset($imageRecord->metadata['thumbnail'] ?? $imageRecord->url),
                'medium_url' => asset($imageRecord->metadata['medium'] ?? $imageRecord->url),
                'alt' => $imageRecord->alt,
                'title' => $imageRecord->title,
                'is_primary' => $imageRecord->is_primary,
                'order' => $imageRecord->order,
            ], 'Image uploaded successfully', 201);

        } catch (\Exception $e) {
            Log::error('ProductController::uploadImage - ERROR', [
                'error' => $e->getMessage(),
                'product_id' => $product->id ?? null,
                'user_id' => auth()->id(),
            ]);

            return $this->error('Failed to upload image: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Delete image from a product.
     */
    public function deleteImage(Request $request, Product $product, $imageId): JsonResponse
    {
        try {
            $store = $this->validateStore();

            // Verify product belongs to the store
            if ($product->store_id !== $store->id) {
                return $this->error('Product not found', 404);
            }

            $image = $product->images()->find($imageId);

            if (!$image) {
                return $this->notFound('Image not found');
            }

            // Delete the main image file from storage
            // URL is stored as 'storage/products/{id}/{filename}'
            $url = $image->url;

            if (str_starts_with($url, 'storage/')) {
                $path = substr($url, 8); // Remove 'storage/' prefix to get the actual path
                \Storage::disk('public')->delete($path);
            }

            // Delete thumbnail and medium variants if they exist
            $metadata = $image->metadata ?? [];
            if (!empty($metadata['thumbnail']) && str_starts_with($metadata['thumbnail'], 'storage/')) {
                $thumbnailPath = substr($metadata['thumbnail'], 8);
                \Storage::disk('public')->delete($thumbnailPath);
            }
            if (!empty($metadata['medium']) && str_starts_with($metadata['medium'], 'storage/')) {
                $mediumPath = substr($metadata['medium'], 8);
                \Storage::disk('public')->delete($mediumPath);
            }

            // Delete the record
            $image->delete();

            return $this->success(null, 'Image deleted successfully');

        } catch (\Exception $e) {
            Log::error('ProductController::deleteImage - ERROR', [
                'error' => $e->getMessage(),
                'product_id' => $product->id ?? null,
                'image_id' => $imageId,
            ]);

            return $this->error('Failed to delete image', 500);
        }
    }

    /**
     * Bulk update product status.
     */
    public function bulkUpdateStatus(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'required|string',
                'status' => 'required|in:active,inactive,draft,archived'
            ]);

            $store = $this->validateStore();

            $updated = $this->productService->bulkUpdateStatus(
                $store,
                $validated['ids'],
                $validated['status']
            );

            return $this->success(null, "{$updated} products updated successfully");

        } catch (\Exception $e) {
            Log::error('ProductController::bulkUpdateStatus - ERROR', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return $this->error('Failed to update products', 500);
        }
    }

    /**
     * Bulk delete products.
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'required|string'
            ]);

            $store = $this->validateStore();

            $deleted = $this->productService->bulkDelete($store, $validated['ids']);

            return $this->success(null, "{$deleted} products deleted successfully");

        } catch (\Exception $e) {
            Log::error('ProductController::bulkDelete - ERROR', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return $this->error('Failed to delete products', 500);
        }
    }

    /**
     * Bulk update product categories.
     */
    public function bulkUpdateCategories(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'required|string',
                'category_ids' => 'required|array',
                'category_ids.*' => 'required|string'
            ]);

            $store = $this->validateStore();

            $updated = $this->productService->bulkUpdateCategories(
                $store,
                $validated['ids'],
                $validated['category_ids']
            );

            return $this->success(null, "{$updated} products updated successfully");

        } catch (\Exception $e) {
            Log::error('ProductController::bulkUpdateCategories - ERROR', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return $this->error('Failed to update categories', 500);
        }
    }

    /**
     * Bulk update product prices.
     */
    public function bulkUpdatePrice(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'required|string',
                'type' => 'required|in:fixed,percentage',
                'value' => 'required|numeric',
                'apply_to' => 'required|in:base_price,sale_price,both'
            ]);

            $store = $this->validateStore();

            $updated = $this->productService->bulkUpdatePrice(
                $store,
                $validated['ids'],
                $validated
            );

            return $this->success(null, "{$updated} products updated successfully");

        } catch (\Exception $e) {
            Log::error('ProductController::bulkUpdatePrice - ERROR', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return $this->error('Failed to update prices', 500);
        }
    }

    /**
     * Export products.
     */
    public function export(Request $request): mixed
    {
        try {
            $validated = $request->validate([
                'ids' => 'nullable|string',
                'format' => 'required|in:csv,xlsx'
            ]);

            $store = $this->validateStore();

            $ids = $validated['ids'] ? explode(',', $validated['ids']) : null;

            return $this->productService->exportProducts($store, $ids, $validated['format']);

        } catch (\Exception $e) {
            Log::error('ProductController::export - ERROR', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return $this->error('Failed to export products', 500);
        }
    }
}
