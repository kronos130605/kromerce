<?php

namespace App\Jobs\Products;

use App\Models\Store;
use App\Repositories\Product\ProductRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ExportProducts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $backoff = 5;
    public int $timeout = 300;

    public function __construct(
        private Store $store,
        private ?array $productIds,
        private string $format,
        private int $userId
    ) {}

    public function handle(ProductRepository $productRepository): void
    {
        try {
            $store = $this->store;
            $ids = $this->productIds;
            $format = $this->format;

            // Get products
            $products = $ids
                ? $productRepository->getBy(['store_id' => $store->id, 'id' => $ids])
                : $productRepository->getBy(['store_id' => $store->id]);

            $filename = 'exports/products_' . $store->id . '_' . now()->format('Y-m-d_His') . '.' . $format;
            $fullPath = storage_path('app/' . $filename);

            // Ensure directory exists
            $directory = dirname($fullPath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            // Generate file based on format
            if ($format === 'csv') {
                $this->generateCsv($products, $fullPath);
            } else {
                $this->generateExcel($products, $fullPath);
            }

            // Store export record for download
            $exportRecord = [
                'filename' => basename($filename),
                'path' => $filename,
                'format' => $format,
                'store_id' => $store->id,
                'user_id' => $this->userId,
                'product_count' => $products->count(),
                'created_at' => now()->toISOString(),
            ];

            // Store metadata in cache for 24 hours (user can download)
            $downloadKey = 'product_export:' . $this->userId . ':' . $exportRecord['filename'];
            cache()->put($downloadKey, $exportRecord, now()->addHours(24));

            Log::info('Products exported successfully', [
                'store_id' => $store->id,
                'user_id' => $this->userId,
                'filename' => $filename,
                'product_count' => $products->count(),
            ]);

            // TODO: Dispatch notification job when implemented
            // SendExportReadyNotification::dispatch($this->userId, $exportRecord);

        } catch (\Exception $e) {
            Log::error('Failed to export products', [
                'store_id' => $this->store->id,
                'user_id' => $this->userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    private function generateCsv($products, string $fullPath): void
    {
        $handle = fopen($fullPath, 'w');

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
                $product->categories->pluck('name')->implode(', '),
            ]);
        }

        fclose($handle);
    }

    private function generateExcel($products, string $fullPath): void
    {
        // For now, fallback to CSV. Can implement Excel later with maatwebsite/excel
        $this->generateCsv($products, $fullPath);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('ExportProducts job failed permanently', [
            'store_id' => $this->store->id,
            'user_id' => $this->userId,
            'error' => $exception->getMessage(),
        ]);
    }
}
