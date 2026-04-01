<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductVariantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    public function __construct(
        private ProductVariantService $variantService
    ) {
        $this->middleware('role:business_owner');
    }

    public function index(Product $product): JsonResponse
    {
        try {
            $this->authorize('view', $product);

            $variants = $this->variantService->getVariantsForProduct($product->id);

            return $this->success($variants);
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve variants', 500);
        }
    }

    public function store(Request $request, Product $product): JsonResponse
    {
        try {
            $this->authorize('update', $product);

            $validated = $request->validate([
                'sku' => 'required|string|max:100|unique:product_variants,sku',
                'name' => 'nullable|string|max:255',
                'price' => 'required|numeric|min:0',
                'sale_price' => 'nullable|numeric|min:0|lt:price',
                'stock_quantity' => 'required|integer|min:0',
                'weight' => 'nullable|numeric|min:0',
                'is_default' => 'boolean',
                'is_active' => 'boolean',
                'attribute_values' => 'array',
                'attribute_values.*' => 'exists:product_attribute_values,id',
            ]);

            $variant = $this->variantService->createVariant($product->id, $validated);

            return $this->success($variant, 'Variant created successfully', 201);
        } catch (\Exception $e) {
            return $this->error('Failed to create variant: ' . $e->getMessage(), 500);
        }
    }

    public function update(Request $request, Product $product, string $variant): JsonResponse
    {
        try {
            $this->authorize('update', $product);

            $validated = $request->validate([
                'sku' => 'sometimes|string|max:100|unique:product_variants,sku,' . $variant,
                'name' => 'nullable|string|max:255',
                'price' => 'sometimes|numeric|min:0',
                'sale_price' => 'nullable|numeric|min:0|lt:price',
                'stock_quantity' => 'sometimes|integer|min:0',
                'weight' => 'nullable|numeric|min:0',
                'is_default' => 'boolean',
                'is_active' => 'boolean',
                'attribute_values' => 'array',
                'attribute_values.*' => 'exists:product_attribute_values,id',
            ]);

            $updated = $this->variantService->updateVariant($variant, $validated);

            return $this->success($updated, 'Variant updated successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to update variant: ' . $e->getMessage(), 500);
        }
    }

    public function destroy(Product $product, string $variant): JsonResponse
    {
        try {
            $this->authorize('update', $product);

            $this->variantService->deleteVariant($variant);

            return $this->success(null, 'Variant deleted successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to delete variant: ' . $e->getMessage(), 500);
        }
    }

    public function bulkUpdate(Request $request, Product $product): JsonResponse
    {
        try {
            $this->authorize('update', $product);

            $validated = $request->validate([
                'variants' => 'required|array',
                'variants.*.id' => 'sometimes|exists:product_variants,id',
                'variants.*.sku' => 'required|string|max:100',
                'variants.*.price' => 'required|numeric|min:0',
                'variants.*.stock_quantity' => 'required|integer|min:0',
            ]);

            $variants = $this->variantService->bulkUpdateVariants(
                $product->id,
                $validated['variants']
            );

            return $this->success($variants, 'Variants updated successfully');
        } catch (\Exception $e) {
            return $this->error('Failed to update variants: ' . $e->getMessage(), 500);
        }
    }
}
