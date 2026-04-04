<?php

namespace App\Repositories\Store;

use App\Models\BusinessCurrencyConfig;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class BusinessCurrencyConfigRepository extends BaseRepository
{
    protected array $allowedFields = [
        'store_id', 'default_currency', 'display_currencies', 'use_custom_rates',
        'auto_update_rates', 'rate_update_frequency', 'last_rate_update',
        'historical_retention_years', 'preferred_cuba_source_id', 'preferred_foreign_source_id',
    ];

    public function __construct(BusinessCurrencyConfig $model)
    {
        parent::__construct($model);
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
     * Get configs with custom rates enabled.
     */
    public function getWithCustomRates(): Collection
    {
        return $this->getBy(['use_custom_rates' => true]);
    }

    /**
     * Get configs with auto update enabled.
     */
    public function getWithAutoUpdate(): Collection
    {
        return $this->getBy(['auto_update_rates' => true]);
    }

    /**
     * Update last rate update timestamp.
     */
    public function updateLastRateUpdate(int $storeId): bool
    {
        return $this->updateBy(['store_id' => $storeId], [
            'last_rate_update' => now()
        ]);
    }

    /**
     * Get or create currency config for store.
     */
    public function getOrCreateForStore(string $storeId): BusinessCurrencyConfig
    {
        return $this->firstOrCreate(
            ['store_id' => $storeId],
            [
                'default_currency' => config('currencies.default', 'USD'),
                'display_currencies' => config('currencies.default_display', ['USD', 'EUR', 'COP']),
                'use_custom_rates' => false,
                'auto_update_rates' => false,
                'rate_update_frequency' => 'weekly',
                'historical_retention_years' => 2,
            ]
        );
    }
}
