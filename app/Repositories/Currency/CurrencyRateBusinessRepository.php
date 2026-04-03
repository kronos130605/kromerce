<?php

namespace App\Repositories\Currency;

use App\Models\CurrencyRateBusiness;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

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
     * Update or create rate for store.
     */
    public function updateOrCreateRate(string $storeId, string $fromCurrency, string $toCurrency, float $rate, string $date, string $source = 'manual'): CurrencyRateBusiness
    {
        $existing = $this->model
            ->where('store_id', $storeId)
            ->where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->where('effective_date', $date)
            ->first();

        if ($existing) {
            $existing->update(['rate' => $rate, 'source' => $source]);
            return $existing;
        }

        return $this->model->create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'store_id' => $storeId,
            'from_currency' => $fromCurrency,
            'to_currency' => $toCurrency,
            'effective_date' => $date,
            'rate' => $rate,
            'source' => $source,
        ]);
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
     * Check if currency code exists in any rate for store.
     */
    public function currencyExists(string $storeId, string $currency): bool
    {
        return $this->model
            ->where('store_id', $storeId)
            ->where(function ($q) use ($currency) {
                $q->where('from_currency', $currency)
                  ->orWhere('to_currency', $currency);
            })
            ->exists();
    }

    /**
     * Delete rate for specific store currency pair.
     */
    public function deleteRate(string $storeId, string $fromCurrency, string $toCurrency): int
    {
        return $this->model
            ->where('store_id', $storeId)
            ->where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->delete();
    }
}
