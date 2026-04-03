<?php

namespace App\Jobs\Products;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class ProcessProductImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 10;
    public int $timeout = 120;

    public function __construct(
        private Product $product,
        private array $imageData,
        private array $metadata = []
    ) {}

    public function handle(): void
    {
        try {
            $product = $this->product;
            $filePath = $this->imageData['temp_path'];
            $filename = $this->imageData['filename'];
            $directory = 'products/' . $product->id;

            // Ensure temp file exists
            if (!file_exists($filePath)) {
                throw new \Exception("Temp file not found: {$filePath}");
            }

            // Store original image
            $originalPath = $directory . '/' . $filename;
            Storage::disk('public')->putFileAs($directory, new \Illuminate\Http\File($filePath), $filename);

            // Create thumbnail (300x300, cropped)
            $thumbnailFilename = 'thumb_' . $filename;
            $thumbnailFullPath = storage_path('app/public/' . $directory . '/' . $thumbnailFilename);

            $image = Image::read($filePath);
            $image->cover(300, 300);
            $image->save($thumbnailFullPath, quality: 80);

            // Create medium size (800x800, fitted)
            $mediumFilename = 'medium_' . $filename;
            $mediumFullPath = storage_path('app/public/' . $directory . '/' . $mediumFilename);

            $mediumImage = Image::read($filePath);
            $mediumImage->scaleDown(800, 800);
            $mediumImage->save($mediumFullPath, quality: 85);

            // Create image record
            $isPrimary = $this->metadata['is_primary'] ?? false;
            $order = $this->metadata['order'] ?? $product->images()->count();

            $imageRecord = $product->images()->create([
                'url' => 'storage/' . $originalPath,
                'alt' => $this->metadata['alt'] ?? $product->name,
                'title' => $this->metadata['title'] ?? null,
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

            // Clean up temp file
            @unlink($filePath);

            Log::info('Product image processed successfully', [
                'product_id' => $product->id,
                'image_id' => $imageRecord->id,
                'filename' => $filename,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to process product image', [
                'product_id' => $this->product->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('ProcessProductImage job failed permanently', [
            'product_id' => $this->product->id,
            'error' => $exception->getMessage(),
        ]);

        // Clean up temp file if exists
        if (isset($this->imageData['temp_path']) && file_exists($this->imageData['temp_path'])) {
            @unlink($this->imageData['temp_path']);
        }
    }
}
