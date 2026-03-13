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
     * Get latest rate for store currency pair.
     */
    public function getLatestRate(string $storeId, string $fromCurrency, string $toCurrency): ?CurrencyRateBusiness
    {
        return $this->model
            ->where('store_id', $storeId)
            ->where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->orderBy('effective_date', 'desc')
            ->first();
    }

    /**
     * Get rate for store on specific date.
     */
    public function getRateForDate(string $storeId, string $fromCurrency, string $toCurrency, string $date): ?CurrencyRateBusiness
    {
        return $this->model
            ->where('store_id', $storeId)
            ->where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->where('effective_date', '<=', $date)
            ->orderBy('effective_date', 'desc')
            ->first();
    }

    /**
     * Get all rates for store.
     */
    public function getAllRatesForStore(string $storeId): Collection
    {
        return $this->getBy(['store_id' => $storeId]);
    }

    /**
     * Get all rates for store on date.
     */
    public function getAllRatesForStoreOnDate(string $storeId, string $date): Collection
    {
        return $this->model
            ->where('store_id', $storeId)
            ->where('effective_date', $date)
            ->get();
    }

    /**
     * Update or create rate for tenant.
     */
    public function updateOrCreateRate(string $storeId, string $fromCurrency, string $toCurrency, float $rate, string $date, string $source = 'manual'): CurrencyRateBusiness
    {
        return $this->updateOrCreate(
            [
                'store_id' => $storeId,
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
    public function getRatesForDateRange(string $storeId, string $fromCurrency, string $toCurrency, string $startDate, string $endDate): Collection
    {
        return $this->model
            ->where('store_id', $storeId)
            ->where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->whereBetween('effective_date', [$startDate, $endDate])
            ->orderBy('effective_date')
            ->get();
    }

    /**
     * Batch update rates for store.
     */
    public function batchUpdateRates(string $storeId, array $rates, string $date, string $source = 'manual'): array
    {
        $results = [];
        
        foreach ($rates as $currencyPair => $rate) {
            try {
                $fromCurrency = $currencyPair['from'];
                $toCurrency = $currencyPair['to'];
                
                $updatedRate = $this->updateOrCreateRate(
                    $storeId,
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
     * Clean up old rates for store.
     */
    public function cleanupOldRates(string $storeId, int $retentionYears): int
    {
        $cutoffDate = now()->subYears($retentionYears)->format('Y-m-d');
        
        return $this->model
            ->where('store_id', $storeId)
            ->where('effective_date', '<', $cutoffDate)
            ->delete();
    }

    /**
     * Delete custom rates for store currency pairs.
     */
    public function deleteCustomRates(string $storeId, array $currencies, string $baseCurrency): int
    {
        $query = $this->model->where('store_id', $storeId);
        
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
     * Get stores with custom rates.
     */
    public function getStoresWithCustomRates(): Collection
    {
        return $this->model
            ->select('store_id')
            ->distinct()
            ->pluck('store_id');
    }

    /**
     * Get rate statistics for tenant.
     */
    public function getRateStatistics(string $storeId): array
    {
        $totalRates = $this->model->where('store_id', $storeId)->count();
        $uniquePairs = $this->model
            ->where('store_id', $storeId)
            ->select('from_currency', 'to_currency')
            ->distinct()
            ->count();
        
        $latestDate = $this->model
            ->where('store_id', $storeId)
            ->max('effective_date');
        
        return [
            'total_rates' => $totalRates,
            'unique_pairs' => $uniquePairs,
            'latest_date' => $latestDate,
        ];
    }
}
