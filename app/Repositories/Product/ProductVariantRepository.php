<?php

namespace App\Repositories\Product;

use App\Models\ProductVariant;
use App\Repositories\BaseRepository;
use Illuminate\Support\Collection;

class ProductVariantRepository extends BaseRepository
{
    public function __construct(ProductVariant $model)
    {
        parent::__construct($model);
    }

    public function getByProductId(string $productId, array $with = []): Collection
    {
        $query = $this->model->where('product_id', $productId);
        
        if (!empty($with)) {
            $query->with($with);
        }
        
        return $query->orderBy('sort_order')->get();
    }

    public function findWithRelations(string $variantId, array $with = []): ?ProductVariant
    {
        $query = $this->model->where('id', $variantId);
        
        if (!empty($with)) {
            $query->with($with);
        }
        
        return $query->first();
    }

    public function syncAttributeValues(ProductVariant $variant, array $attributeValueIds): void
    {
        $variant->attributeValues()->sync($attributeValueIds);
    }

    public function getDefaultVariant(string $productId): ?ProductVariant
    {
        return $this->model
            ->where('product_id', $productId)
            ->where('is_default', true)
            ->first();
    }

    public function getActiveVariants(string $productId): Collection
    {
        return $this->model
            ->where('product_id', $productId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    public function getInStockVariants(string $productId): Collection
    {
        return $this->model
            ->where('product_id', $productId)
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->orderBy('sort_order')
            ->get();
    }

    public function updateStock(string $variantId, int $quantity): bool
    {
        return $this->updateBy(
            ['id' => $variantId],
            ['stock_quantity' => $quantity]
        ) > 0;
    }

    public function decrementStock(string $variantId, int $quantity): bool
    {
        $variant = $this->getById($variantId);
        
        if (!$variant || $variant->stock_quantity < $quantity) {
            return false;
        }
        
        return $variant->decrement('stock_quantity', $quantity) > 0;
    }

    public function incrementStock(string $variantId, int $quantity): bool
    {
        $variant = $this->getById($variantId);
        
        if (!$variant) {
            return false;
        }
        
        return $variant->increment('stock_quantity', $quantity) > 0;
    }
}
