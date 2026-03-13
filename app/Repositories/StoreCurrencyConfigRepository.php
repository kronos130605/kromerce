<?php

namespace App\Repositories;

use App\Models\StoreCurrencyConfig;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class StoreCurrencyConfigRepository extends BaseRepository
{
    protected array $allowedFields = [
        'store_id', 'default_currency', 'display_currencies', 'use_custom_rates',
        'auto_update_rates', 'rate_update_frequency', 'last_rate_update',
        'historical_retention_years', 'created_at', 'updated_at'
    ];

    public function __construct(StoreCurrencyConfig $model)
    {
        parent::__construct($model);
    }

    /**
     * Get config by store ID.
     */
    public function getByStoreId(int $storeId): ?StoreCurrencyConfig
    {
        return $this->getFirstBy(['store_id' => $storeId]);
    }

    /**
     * Get or create config for store.
     */
    public function getOrCreateForStore(int $storeId): StoreCurrencyConfig
    {
        return $this->firstOrCreate(
            ['store_id' => $storeId],
            [
                'default_currency' => 'USD',
                'display_currencies' => ['USD', 'EUR', 'CUP'],
                'use_custom_rates' => false,
                'auto_update_rates' => false,
                'rate_update_frequency' => 'weekly',
                'historical_retention_years' => 2,
            ]
        );
    }

    /**
     * Update config for store.
     */
    public function updateForStore(int $storeId, array $data): bool
    {
        return $this->updateBy(['store_id' => $storeId], $data);
    }

    /**
     * Get configs that need rate updates.
     */
    public function getNeedingRateUpdate(): Collection
    {
        return $this->model
            ->where('auto_update_rates', true)
            ->where(function ($query) {
                $query->whereNull('last_rate_update')
                      ->orWhere('last_rate_update', '<', now()->subDays(7)->format('Y-m-d'));
            })
            ->get();
    }
}
