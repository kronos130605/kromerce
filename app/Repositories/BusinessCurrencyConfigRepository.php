<?php

namespace App\Repositories;

use App\Models\BusinessCurrencyConfig;
use Illuminate\Database\Eloquent\Collection;

class BusinessCurrencyConfigRepository extends BaseRepository
{
    public function __construct(BusinessCurrencyConfig $model)
    {
        parent::__construct($model);
    }

    /**
     * Get config by tenant ID.
     */
    public function getByTenantId(string $tenantId): ?BusinessCurrencyConfig
    {
        return $this->getFirstBy(['tenant_id' => $tenantId]);
    }

    /**
     * Get or create config for tenant.
     */
    public function getOrCreateForTenant(string $tenantId): BusinessCurrencyConfig
    {
        return $this->firstOrCreate(
            ['tenant_id' => $tenantId],
            [
                'default_currency' => 'USD',
                'display_currencies' => ['USD', 'EUR', 'GBP'],
                'use_custom_rates' => false,
                'auto_update_rates' => true,
                'rate_update_frequency' => 'daily',
                'historical_retention_years' => 2,
            ]
        );
    }

    /**
     * Update config for tenant.
     */
    public function updateForTenant(string $tenantId, array $data): bool
    {
        return $this->updateBy(['tenant_id' => $tenantId], $data);
    }

    /**
     * Get configs that need rate updates.
     */
    public function getNeedingRateUpdate(string $frequency = 'daily'): Collection
    {
        $lastUpdateThreshold = now()->subDay()->format('Y-m-d');
        
        return $this->model
            ->where('auto_update_rates', true)
            ->where('rate_update_frequency', $frequency)
            ->where(function ($query) use ($lastUpdateThreshold) {
                $query->whereNull('last_rate_update')
                      ->orWhere('last_rate_update', '<', $lastUpdateThreshold);
            })
            ->get();
    }

    /**
     * Get configs using custom rates.
     */
    public function getUsingCustomRates(): Collection
    {
        return $this->getBy(['use_custom_rates' => true]);
    }

    /**
     * Get configs by retention years.
     */
    public function getByRetentionYears(int $years): Collection
    {
        return $this->getBy(['historical_retention_years' => $years]);
    }
}
