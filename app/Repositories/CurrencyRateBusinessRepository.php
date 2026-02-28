<?php

namespace App\Repositories;

use App\Models\CurrencyRateBusiness;
use Illuminate\Database\Eloquent\Collection;

class CurrencyRateBusinessRepository extends BaseRepository
{
    public function __construct(CurrencyRateBusiness $model)
    {
        parent::__construct($model);
    }

    /**
     * Get latest rate for tenant currency pair.
     */
    public function getLatestRate(string $tenantId, string $fromCurrency, string $toCurrency): ?CurrencyRateBusiness
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->orderBy('effective_date', 'desc')
            ->first();
    }

    /**
     * Get rate for tenant on specific date.
     */
    public function getRateForDate(string $tenantId, string $fromCurrency, string $toCurrency, string $date): ?CurrencyRateBusiness
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->where('effective_date', '<=', $date)
            ->orderBy('effective_date', 'desc')
            ->first();
    }

    /**
     * Get all rates for tenant.
     */
    public function getAllRatesForTenant(string $tenantId): Collection
    {
        return $this->getBy(['tenant_id' => $tenantId]);
    }

    /**
     * Get all rates for tenant on date.
     */
    public function getAllRatesForTenantOnDate(string $tenantId, string $date): Collection
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->where('effective_date', $date)
            ->get();
    }

    /**
     * Update or create rate for tenant.
     */
    public function updateOrCreateRate(string $tenantId, string $fromCurrency, string $toCurrency, float $rate, string $date, string $source = 'manual'): CurrencyRateBusiness
    {
        return $this->updateOrCreate(
            [
                'tenant_id' => $tenantId,
                'from_currency' => $fromCurrency,
                'to_currency' => $toCurrency,
                'effective_date' => $date,
            ],
            [
                'rate' => $rate,
                'source' => $source,
            ]
        );
    }

    /**
     * Get rates for date range.
     */
    public function getRatesForDateRange(string $tenantId, string $fromCurrency, string $toCurrency, string $startDate, string $endDate): Collection
    {
        return $this->model
            ->where('tenant_id', $tenantId)
            ->where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->whereBetween('effective_date', [$startDate, $endDate])
            ->orderBy('effective_date')
            ->get();
    }

    /**
     * Batch update rates for tenant.
     */
    public function batchUpdateRates(string $tenantId, array $rates, string $date, string $source = 'manual'): array
    {
        $results = [];
        
        foreach ($rates as $currencyPair => $rate) {
            try {
                $fromCurrency = $currencyPair['from'];
                $toCurrency = $currencyPair['to'];
                
                $updatedRate = $this->updateOrCreateRate(
                    $tenantId,
                    $fromCurrency,
                    $toCurrency,
                    $rate,
                    $date,
                    $source
                );
                
                $results[] = [
                    'from_currency' => $fromCurrency,
                    'to_currency' => $toCurrency,
                    'rate' => $rate,
                    'success' => true,
                    'id' => $updatedRate->id,
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'from_currency' => $currencyPair['from'] ?? 'unknown',
                    'to_currency' => $currencyPair['to'] ?? 'unknown',
                    'rate' => $rate,
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }
        
        return $results;
    }

    /**
     * Clean up old rates for tenant.
     */
    public function cleanupOldRates(string $tenantId, int $retentionYears): int
    {
        $cutoffDate = now()->subYears($retentionYears)->format('Y-m-d');
        
        return $this->model
            ->where('tenant_id', $tenantId)
            ->where('effective_date', '<', $cutoffDate)
            ->delete();
    }

    /**
     * Delete custom rates for tenant currency pairs.
     */
    public function deleteCustomRates(string $tenantId, array $currencies, string $baseCurrency): int
    {
        $query = $this->model->where('tenant_id', $tenantId);
        
        foreach ($currencies as $targetCurrency) {
            if ($targetCurrency !== $baseCurrency) {
                $query->orWhere(function ($q) use ($baseCurrency, $targetCurrency) {
                    $q->where('from_currency', $baseCurrency)
                      ->where('to_currency', $targetCurrency);
                });
            }
        }
        
        return $query->delete();
    }

    /**
     * Get tenants with custom rates.
     */
    public function getTenantsWithCustomRates(): Collection
    {
        return $this->model
            ->select('tenant_id')
            ->distinct()
            ->pluck('tenant_id');
    }

    /**
     * Get rate statistics for tenant.
     */
    public function getRateStatistics(string $tenantId): array
    {
        $totalRates = $this->model->where('tenant_id', $tenantId)->count();
        $uniquePairs = $this->model
            ->where('tenant_id', $tenantId)
            ->select('from_currency', 'to_currency')
            ->distinct()
            ->count();
        
        $latestDate = $this->model
            ->where('tenant_id', $tenantId)
            ->max('effective_date');
        
        return [
            'total_rates' => $totalRates,
            'unique_pairs' => $uniquePairs,
            'latest_date' => $latestDate,
        ];
    }
}
