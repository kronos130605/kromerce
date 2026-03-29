<?php

namespace App\Services;

use App\Repositories\Product\ProductVariantRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductVariantService
{
    public function __construct(
        private ProductVariantRepository $variantRepository
    ) {}

    public function getVariantsForProduct(string $productId): Collection
    {
        return $this->variantRepository->getByProductId($productId, [
            'images',
            'attributeValues.attribute'
        ]);
    }

    public function createVariant(string $productId, array $data): mixed
    {
        return DB::transaction(function () use ($productId, $data) {
            $attributeValues = $data['attribute_values'] ?? [];
            unset($data['attribute_values']);

            $data['product_id'] = $productId;
            
            $variant = $this->variantRepository->create($data);

            if (!empty($attributeValues)) {
                $this->variantRepository->syncAttributeValues($variant, $attributeValues);
            }

            return $this->variantRepository->findWithRelations($variant->id, [
                'images',
                'attributeValues.attribute'
            ]);
        });
    }

    public function updateVariant(string $variantId, array $data): mixed
    {
        return DB::transaction(function () use ($variantId, $data) {
            $attributeValues = $data['attribute_values'] ?? null;
            unset($data['attribute_values']);

            $this->variantRepository->updateBy(['id' => $variantId], $data);

            if ($attributeValues !== null) {
                $variant = $this->variantRepository->getById($variantId);
                $this->variantRepository->syncAttributeValues($variant, $attributeValues);
            }

            return $this->variantRepository->findWithRelations($variantId, [
                'images',
                'attributeValues.attribute'
            ]);
        });
    }

    public function deleteVariant(string $variantId): bool
    {
        return $this->variantRepository->deleteBy(['id' => $variantId]) > 0;
    }

    public function getVariantById(string $variantId): mixed
    {
        return $this->variantRepository->findWithRelations($variantId, [
            'images',
            'attributeValues.attribute',
            'product'
        ]);
    }

    public function updateStock(string $variantId, int $quantity): bool
    {
        return $this->variantRepository->updateStock($variantId, $quantity);
    }

    public function bulkUpdateVariants(string $productId, array $variants): Collection
    {
        return DB::transaction(function () use ($productId, $variants) {
            $updated = collect();

            foreach ($variants as $variantData) {
                if (isset($variantData['id'])) {
                    $variant = $this->updateVariant($variantData['id'], $variantData);
                } else {
                    $variant = $this->createVariant($productId, $variantData);
                }
                $updated->push($variant);
            }

            return $updated;
        });
    }
}
