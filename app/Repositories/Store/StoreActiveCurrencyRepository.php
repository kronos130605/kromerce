<?php

namespace App\Repositories\Store;

use App\Models\StoreActiveCurrency;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class StoreActiveCurrencyRepository extends BaseRepository
{
    protected array $allowedFields = [
        'store_id', 'currency_code', 'is_active', 'sort_order',
    ];

    public function __construct(StoreActiveCurrency $model)
    {
        parent::__construct($model);
    }

    /**
     * Get all active currencies for a store, ordered by sort_order.
     */
    public function getActiveForStore(int|string $storeId): Collection
    {
        return $this->model->newQuery()
            ->where('store_id', $storeId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('currency_code')
            ->get();
    }

    /**
     * Get all currencies (active and inactive) for a store.
     */
    public function getAllForStore(int|string $storeId): Collection
    {
        return $this->model->newQuery()
            ->where('store_id', $storeId)
            ->orderBy('sort_order')
            ->orderBy('currency_code')
            ->get();
    }

    /**
     * Get active currency codes array for a store.
     */
    public function getActiveCodesForStore(int|string $storeId): array
    {
        return $this->model->newQuery()
            ->where('store_id', $storeId)
            ->where('is_active', true)
            ->pluck('currency_code')
            ->toArray();
    }

    /**
     * Check if a specific currency is active for a store.
     */
    public function isCurrencyActiveForStore(int|string $storeId, string $currencyCode): bool
    {
        return $this->model->newQuery()
            ->where('store_id', $storeId)
            ->where('currency_code', $currencyCode)
            ->where('is_active', true)
            ->exists();
    }
}
