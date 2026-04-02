<?php

namespace App\Http\Resources\Storefront;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Product Card Resource for Home Page
 * Lightweight resource optimized for product cards in listings
 */
class ProductCardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->short_description ?? $this->description,
            'base_price' => $this->base_price,
            'sale_price' => $this->sale_price,
            'base_currency' => $this->base_currency,
            'stock_quantity' => $this->stock_quantity,
            'is_on_sale' => $this->is_on_sale,
            'condition' => $this->condition,
            'rating' => $this->rating,
            'reviews_count' => $this->reviews_count,
            'sales_count' => $this->sales_count,
            'is_new' => $this->is_new,
            'featured' => $this->featured,
            // Images array for frontend compatibility
            'images' => $this->whenLoaded('images', function () {
                return $this->images->map(fn ($img) => [
                    'id' => $img->id,
                    'url' => $this->formatImageUrl($img->url),
                    'thumbnail_url' => $this->formatImageUrl($img->thumbnail_url),
                    'alt_text' => $img->alt_text,
                    'is_primary' => $img->is_primary,
                ])->toArray();
            }, []),
            // Minimal store info for card
            'store' => $this->store ? [
                'id' => $this->store->id,
                'name' => $this->store->name,
                'slug' => $this->store->slug,
            ] : null,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }

    /**
     * Format image URL to be absolute.
     */
    private function formatImageUrl(?string $url): ?string
    {
        if (!$url) {
            return null;
        }

        return str_starts_with($url, 'http') ? $url : asset($url);
    }
}
