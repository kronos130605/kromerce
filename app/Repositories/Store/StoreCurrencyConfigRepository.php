<?php

namespace App\Repositories\Store;

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
