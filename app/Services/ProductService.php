<?php

namespace App\Services;

use App\Http\Resources\ProductResource;
use App\Models\Store;
use App\Models\User;
use App\Repositories\Product\ProductCategoryRepository;
use App\Repositories\Product\ProductRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProductService
{
    public function __construct(
        private ProductRepository $productRepository,
        private ProductCategoryRepository $productCategoryRepository
    ) {}

    /**
     * Get products for store with filters.
     */
    public function getProductsForStore(Store $store, array $filters = []): LengthAwarePaginator
    {
        try {
            $paginator = $this->productRepository->paginateForStore($store->id, $filters);
            
            // Transform items using ProductResource to ensure proper serialization
            $transformedItems = ProductResource::collection($paginator->getCollection())->toArray(request());
            
            return new LengthAwarePaginator(
                $transformedItems,
                $paginator->total(),
                $paginator->perPage(),
                $paginator->currentPage(),
                ['path' => $paginator->path()]
            );
        } catch (\Exception $e) {
            throw new \Exception('Failed to retrieve products: ' . $e->getMessage());
        }
    }

    /**
     * Get all categories (global).
     */
    public function getCategories(): Collection
    {
        try {
            return $this->productCategoryRepository->getAll();
        } catch (\Exception $e) {
            throw new \Exception('Failed to retrieve categories: ' . $e->getMessage());
        }
    }

    /**
     * Get product statistics for store.
     */
    public function getStatisticsForStore(Store $store): array
    {
        try {
            return $this->productRepository->getStatistics($store->id);
        } catch (\Exception $e) {
            throw new \Exception('Failed to retrieve statistics: ' . $e->getMessage());
        }
    }

    /**
     * Create product for store.
     */
    public function createProductForStore(Store $store, User $user, array $data): Model
    {
        try {
            return \DB::transaction(function () use ($store, $user, $data) {
                $data['store_id'] = $store->id;
                $data['created_by'] = $user->id;

                // Extract category_ids before creating product
                $categoryIds = $data['category_ids'] ?? [];
                unset($data['category_ids']);

                $product = $this->productRepository->create($data);

                // Sync categories if provided
                if (!empty($categoryIds)) {
                    $product->categories()->sync($categoryIds);
                }

                return $product;
            });
        } catch (\Exception $e) {
            throw new \Exception('Failed to create product: ' . $e->getMessage());
        }
    }

    /**
     * Get product by ID for store.
     */
    public function getProductForStore(Store $store, string $productId): ?Model
    {
        try {
            return $this->productRepository
                ->getFirstBy(['id' => $productId, 'store_id' => $store->id]);
        } catch (\Exception $e) {
            throw new \Exception('Failed to retrieve product: ' . $e->getMessage());
        }
    }

    /**
     * Update product for store.
     */
    public function updateProductForStore(Store $store, string $productId, array $data): bool
    {
        try {
            return \DB::transaction(function () use ($store, $productId, $data) {
                // Extract category_ids before updating product
                $categoryIds = $data['category_ids'] ?? null;
                unset($data['category_ids']);
                
                // Add updated_by
                $data['updated_by'] = auth()->id();

                $updated = $this->productRepository
                    ->updateBy(['id' => $productId, 'store_id' => $store->id], $data);

                // Sync categories if provided
                if ($categoryIds !== null && $updated > 0) {
                    $product = $this->productRepository->getById($productId);
                    if ($product) {
                        $product->categories()->sync($categoryIds);
                    }
                }

                return $updated > 0;
            });
        } catch (\Exception $e) {
            throw new \Exception('Failed to update product: ' . $e->getMessage());
        }
    }

    /**
     * Get latest products for store.
     */
    public function getLatestProductsForStore(Store $store, int $limit = 5): Collection
    {
        try {
            return $this->productRepository->getLatestForStore($store->id, $limit);
        } catch (\Exception $e) {
            throw new \Exception('Failed to retrieve latest products: ' . $e->getMessage());
        }
    }

    /**
     * Get low stock products for store.
     */
    public function getLowStockProductsForStore(Store $store, int $limit = 5): Collection
    {
        try {
            return $this->productRepository->getLowStock($store->id)->take($limit);
        } catch (\Exception $e) {
            throw new \Exception('Failed to retrieve low stock products: ' . $e->getMessage());
        }
    }

    /**
     * Delete product for store.
     */
    public function deleteProductForStore(Store $store, string $productId): bool
    {
        try {
            $deleted = $this->productRepository
                ->deleteBy(['id' => $productId, 'store_id' => $store->id]);

            return $deleted > 0;
        } catch (\Exception $e) {
            throw new \Exception('Failed to delete product: ' . $e->getMessage());
        }
    }

    /**
     * Bulk update product status.
     */
    public function bulkUpdateStatus(Store $store, array $ids, string $status): int
    {
        try {
            return \DB::transaction(function () use ($store, $ids, $status) {
                return $this->productRepository->updateBy(
                    ['store_id' => $store->id, 'id' => $ids],
                    ['status' => $status, 'updated_by' => auth()->id()]
                );
            });
        } catch (\Exception $e) {
            throw new \Exception('Failed to bulk update status: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete products.
     */
    public function bulkDelete(Store $store, array $ids): int
    {
        try {
            return \DB::transaction(function () use ($store, $ids) {
                // Delete images first for each product
                $products = $this->productRepository->getBy([
                    'store_id' => $store->id,
                    'id' => $ids
                ]);

                foreach ($products as $product) {
                    $directory = 'products/' . $product->id;
                    \Storage::disk('public')->deleteDirectory($directory);
                }

                return $this->productRepository->deleteBy([
                    'store_id' => $store->id,
                    'id' => $ids
                ]);
            });
        } catch (\Exception $e) {
            throw new \Exception('Failed to bulk delete products: ' . $e->getMessage());
        }
    }

    /**
     * Bulk update product categories.
     */
    public function bulkUpdateCategories(Store $store, array $ids, array $categoryIds): int
    {
        try {
            return \DB::transaction(function () use ($store, $ids, $categoryIds) {
                $products = $this->productRepository->getBy([
                    'store_id' => $store->id,
                    'id' => $ids
                ]);

                $updated = 0;
                foreach ($products as $product) {
                    $product->categories()->sync($categoryIds);
                    $product->update(['updated_by' => auth()->id()]);
                    $updated++;
                }

                return $updated;
            });
        } catch (\Exception $e) {
            throw new \Exception('Failed to bulk update categories: ' . $e->getMessage());
        }
    }

    /**
     * Bulk update product prices.
     */
    public function bulkUpdatePrice(Store $store, array $ids, array $data): int
    {
        try {
            return \DB::transaction(function () use ($store, $ids, $data) {
                $products = $this->productRepository->getBy([
                    'store_id' => $store->id,
                    'id' => $ids
                ]);

                $updated = 0;
                foreach ($products as $product) {
                    $updateData = ['updated_by' => auth()->id()];

                    if ($data['type'] === 'fixed') {
                        if (in_array($data['apply_to'], ['base_price', 'both'])) {
                            $updateData['base_price'] = $data['value'];
                        }
                        if (in_array($data['apply_to'], ['sale_price', 'both'])) {
                            $updateData['base_sale_price'] = $data['value'];
                            $updateData['is_on_sale'] = true;
                        }
                    } else { // percentage
                        $factor = 1 + ($data['value'] / 100);
                        if (in_array($data['apply_to'], ['base_price', 'both'])) {
                            $updateData['base_price'] = round($product->base_price * $factor, 2);
                        }
                        if (in_array($data['apply_to'], ['sale_price', 'both']) && $product->base_sale_price) {
                            $updateData['base_sale_price'] = round($product->base_sale_price * $factor, 2);
                        }
                    }

                    $this->productRepository->update($product->id, $updateData);
                    $updated++;
                }

                return $updated;
            });
        } catch (\Exception $e) {
            throw new \Exception('Failed to bulk update prices: ' . $e->getMessage());
        }
    }

    /**
     * Export products.
     */
    public function exportProducts(Store $store, ?array $ids, string $format): mixed
    {
        try {
            $products = $ids
                ? $this->productRepository->getBy(['store_id' => $store->id, 'id' => $ids])
                : $this->productRepository->getBy(['store_id' => $store->id]);

            $filename = 'products_' . now()->format('Y-m-d_His') . '.' . $format;

            if ($format === 'csv') {
                return $this->exportToCsv($products, $filename);
            }

            // Default to CSV for now, can add Excel later
            return $this->exportToCsv($products, $filename);
        } catch (\Exception $e) {
            throw new \Exception('Failed to export products: ' . $e->getMessage());
        }
    }

    /**
     * Export products to CSV.
     */
    private function exportToCsv(Collection $products, string $filename): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->stream(function () use ($products) {
            $handle = fopen('php://output', 'w');

            // Headers
            fputcsv($handle, ['ID', 'Name', 'SKU', 'Base Price', 'Sale Price', 'Status', 'Stock', 'Categories']);

            foreach ($products as $product) {
                fputcsv($handle, [
                    $product->id,
                    $product->name,
                    $product->sku,
                    $product->base_price,
                    $product->base_sale_price,
                    $product->status,
                    $product->stock_quantity,
                    $product->categories->pluck('name')->implode(', ')
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
