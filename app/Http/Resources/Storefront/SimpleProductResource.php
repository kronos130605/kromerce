<?php

namespace App\Http\Resources\Storefront;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SimpleProductResource extends JsonResource
{
    /**
     * Transform the resource into an array for listings.
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
            'sales_count' => $this->sales_count,
            'images' => $this->whenLoaded('images', fn () => $this->images->map(fn ($img) => [
                'id' => $img->id,
                'url' => $img->url,
                'thumbnail_url' => $img->thumbnail_url,
                'alt_text' => $img->alt_text,
                'is_primary' => $img->is_primary,
            ]), []),
            'store' => $this->whenLoaded('store', fn () => [
                'id' => $this->store->id,
                'name' => $this->store->name,
                'slug' => $this->store->slug,
            ]),
        ];
    }
}
