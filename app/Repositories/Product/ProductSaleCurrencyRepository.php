<?php

namespace App\Repositories\Product;

use App\Models\ProductSaleCurrency;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class ProductSaleCurrencyRepository extends BaseRepository
{
    protected array $allowedFields = [
        'product_id', 'currency_code', 'is_enabled',
    ];

    public function __construct(ProductSaleCurrency $model)
    {
        parent::__construct($model);
    }

    /**
     * Get enabled sale currencies for a product.
     */
    public function getEnabledForProduct(int|string $productId): Collection
    {
        return $this->model->newQuery()
            ->where('product_id', $productId)
            ->where('is_enabled', true)
            ->orderBy('currency_code')
            ->get();
    }

    /**
     * Get enabled currency codes for a product.
     */
    public function getEnabledCodesForProduct(int|string $productId): array
    {
        return $this->model->newQuery()
            ->where('product_id', $productId)
            ->where('is_enabled', true)
            ->pluck('currency_code')
            ->toArray();
    }

    /**
     * Sync sale currencies for a product (upsert list, disable removed ones).
     */
    public function syncForProduct(int|string $productId, array $currencyCodes): void
    {
        $existing = $this->model->newQuery()
            ->where('product_id', $productId)
            ->pluck('currency_code')
            ->toArray();

        foreach ($currencyCodes as $code) {
            $this->model->newQuery()->updateOrCreate(
                ['product_id' => $productId, 'currency_code' => $code],
                ['is_enabled' => true]
            );
        }

        $toDisable = array_diff($existing, $currencyCodes);
        if (!empty($toDisable)) {
            $this->model->newQuery()
                ->where('product_id', $productId)
                ->whereIn('currency_code', $toDisable)
                ->update(['is_enabled' => false]);
        }
    }

    /**
     * Count products actively using a currency code (across a store).
     */
    public function countActiveProductsWithCurrency(int|string $storeId, string $currencyCode): int
    {
        return $this->model->newQuery()
            ->where('currency_code', $currencyCode)
            ->where('is_enabled', true)
            ->whereHas('product', fn ($q) => $q->where('store_id', $storeId)->where('status', 'active'))
            ->count();
    }

    /**
     * Get active products using a currency (for migration listing).
     */
    public function getActiveProductsWithCurrency(int|string $storeId, string $currencyCode): Collection
    {
        return $this->model->newQuery()
            ->where('currency_code', $currencyCode)
            ->where('is_enabled', true)
            ->with('product:id,name,sku,status,base_currency')
            ->whereHas('product', fn ($q) => $q->where('store_id', $storeId)->where('status', 'active'))
            ->get();
    }

    /**
     * Disable a currency for all products of a store.
     */
    public function disableCurrencyForStore(int|string $storeId, string $currencyCode): int
    {
        return $this->model->newQuery()
            ->where('currency_code', $currencyCode)
            ->whereHas('product', fn ($q) => $q->where('store_id', $storeId))
            ->update(['is_enabled' => false]);
    }

    /**
     * Migrate all products using a currency to another currency.
     */
    public function migrateCurrencyForStore(int|string $storeId, string $fromCode, string $toCode): int
    {
        return $this->model->newQuery()
            ->where('currency_code', $fromCode)
            ->where('is_enabled', true)
            ->whereHas('product', fn ($q) => $q->where('store_id', $storeId))
            ->update(['currency_code' => $toCode]);
    }
}
