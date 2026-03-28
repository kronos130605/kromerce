<?php

namespace App\Http\Controllers;

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

            // Create image record with all sizes
            $baseUrl = config('app.url') . ':8080';
            $imageRecord = $product->images()->create([
                'url' => $baseUrl . '/storage/' . $originalPath,
                'alt' => $request->input('alt', $product->name),
                'title' => $request->input('title'),
                'is_primary' => $isPrimary,
                'order' => $order,
                'metadata' => [
                    'thumbnail' => $baseUrl . '/storage/' . $directory . '/' . $thumbnailFilename,
                    'medium' => $baseUrl . '/storage/' . $directory . '/' . $mediumFilename,
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
                'url' => $imageRecord->url,
                'thumbnail_url' => $imageRecord->metadata['thumbnail'] ?? null,
                'medium_url' => $imageRecord->metadata['medium'] ?? null,
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

            // Delete the file from storage
            $path = str_replace(asset('storage/'), '', $image->url);
            \Storage::disk('public')->delete($path);

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
}
