<?php

namespace App\Http\Resources\Storefront;

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
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'base_price' => $this->base_price,
            'sale_price' => $this->sale_price,
            'base_currency' => $this->base_currency,
            'stock_quantity' => $this->stock_quantity,
            'status' => $this->status,
            'is_on_sale' => $this->is_on_sale,
            'condition' => $this->condition,
            'sku' => $this->sku,
            'rating' => $this->rating,
            'reviews_count' => $this->reviews_count,
            'sales_count' => $this->sales_count,
            'featured' => $this->featured,
            'is_new' => $this->is_new,
            'images' => $this->whenLoaded('images', fn () => $this->images->map(fn ($img) => [
                'id' => $img->id,
                'url' => $img->url,
                'thumbnail_url' => $img->thumbnail_url,
                'alt_text' => $img->alt_text,
                'is_primary' => $img->is_primary,
            ])),
            'variants' => $this->whenLoaded('variants', fn () => $this->variants->map(fn ($v) => [
                'id' => $v->id,
                'name' => $v->name,
                'sku' => $v->sku,
                'price_adjustment' => $v->price_adjustment,
                'stock_quantity' => $v->stock_quantity,
            ])),
            'categories' => $this->whenLoaded('categories', fn () => $this->categories->map(fn ($cat) => [
                'id' => $cat->id,
                'name' => $cat->name,
                'slug' => $cat->slug,
            ])),
            'store' => $this->whenLoaded('store', fn () => [
                'id' => $this->store->id,
                'name' => $this->store->name,
                'slug' => $this->store->slug,
                'logo' => $this->store->logo,
                'status' => $this->store->status,
                'verified_business' => $this->store->verified_business,
            ]),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
