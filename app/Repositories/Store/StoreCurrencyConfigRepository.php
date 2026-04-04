<?php

namespace App\Repositories\Store;

use App\Models\StoreCurrencyConfig;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class StoreCurrencyConfigRepository extends BaseRepository
{
    protected array $allowedFields = [
        'store_id', 'default_currency', 'display_currencies', 'dashboard_pairs',
        'use_custom_rates', 'auto_update_rates', 'rate_update_frequency',
        'last_rate_update', 'historical_retention_years',
        'preferred_cuba_source_id', 'preferred_foreign_source_id',
        'created_at', 'updated_at',
    ];

    public function __construct(StoreCurrencyConfig $model)
    {
        parent::__construct($model);
    }

    /**
     * Get or create currency config for store.
     */
    public function getOrCreateForStore(int|string $storeId): StoreCurrencyConfig
    {
        return $this->firstOrCreate(
            ['store_id' => $storeId],
            [
                'default_currency'          => config('currencies.default', 'USD'),
                'display_currencies'        => config('currencies.default_display', ['USD', 'EUR', 'CUP']),
                'use_custom_rates'          => false,
                'auto_update_rates'         => false,
                'rate_update_frequency'     => 'weekly',
                'historical_retention_years' => 2,
            ]
        );
    }

    /**
     * Get configs that need rate updates.
     */
    public function getNeedingRateUpdate(): Collection
    {
        return $this->model->newQuery()
            ->where('auto_update_rates', true)
            ->where(function ($query) {
                $query->whereNull('last_rate_update')
                      ->orWhere('last_rate_update', '<', now()->subDays(7)->format('Y-m-d'));
            })
            ->get();
    }

    /**
     * Update last rate update timestamp.
     */
    public function updateLastRateUpdate(int|string $storeId): bool
    {
        return $this->updateBy(['store_id' => $storeId], [
            'last_rate_update' => now()->format('Y-m-d'),
        ]) > 0;
    }
}
