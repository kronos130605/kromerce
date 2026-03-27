<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id, // Force ID to string
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'base_currency' => $this->base_currency,
            'base_price' => $this->base_price,
            'base_sale_price' => $this->base_sale_price,
            'cost_price' => $this->cost_price,
            'is_on_sale' => $this->is_on_sale,
            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'status' => $this->status,
            'visibility' => $this->visibility,
            'featured' => $this->featured,
            'manage_stock' => $this->manage_stock,
            'stock_quantity' => $this->stock_quantity,
            'low_stock_threshold' => $this->low_stock_threshold,
            'stock_status' => $this->stock_status,
            'category_ids' => $this->categories?->pluck('id')->toArray() ?? [],
            'tags' => $this->tags?->pluck('name')->toArray() ?? [],
            'images' => $this->images?->map(fn($img) => [
                'id' => (string) $img->id,
                'url' => $img->url,
                'alt' => $img->alt,
                'title' => $img->title,
                'is_primary' => $img->is_primary,
                'order' => $img->order,
            ])->toArray() ?? [],
            'seo_title' => $this->seo_title,
            'seo_description' => $this->seo_description,
            'seo_keywords' => $this->seo_keywords,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
