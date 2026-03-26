<?php

namespace App\Repositories\Currency;

use App\Models\CurrencyRateGlobal;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class CurrencyRateGlobalRepository extends BaseRepository
{
    public function __construct(CurrencyRateGlobal $model)
    {
        parent::__construct($model);
    }

    /**
     * Get latest rate for currency pair.
     */
    public function getLatestRate(string $fromCurrency, string $toCurrency): ?CurrencyRateGlobal
    {
        return $this->model
            ->where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->orderBy('effective_date', 'desc')
            ->first();
    }

    /**
     * Get rate for specific date.
     */
    public function getRateForDate(string $fromCurrency, string $toCurrency, string $date): ?CurrencyRateGlobal
    {
        return $this->model
            ->where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->where('effective_date', '<=', $date)
            ->orderBy('effective_date', 'desc')
            ->first();
    }

    /**
     * Get rates for date range.
     */
    public function getRatesForDateRange(string $fromCurrency, string $toCurrency, string $startDate, string $endDate): Collection
    {
        return $this->model
            ->where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->whereBetween('effective_date', [$startDate, $endDate])
            ->orderBy('effective_date')
            ->get();
    }

    /**
     * Update or create rate.
     */
    public function updateOrCreateRate(string $fromCurrency, string $toCurrency, float $rate, string $date, string $source = 'api'): CurrencyRateGlobal
    {
        return $this->updateOrCreate(
            [
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
     * Get latest rates for multiple currency pairs.
     */
    public function getLatestRatesForPairs(array $currencyPairs, string $date = null): Collection
    {
        $query = $this->model->query();

        foreach ($currencyPairs as $pair) {
            $query->orWhere(function ($q) use ($pair, $date) {
                $q->where('from_currency', $pair['from'])
                  ->where('to_currency', $pair['to']);
            });
        }

        if ($date) {
            $query->where('effective_date', '<=', $date);
        }

        return $query->orderBy('effective_date', 'desc')->get();
    }

    /**
     * Clean up old rates.
     */
    public function cleanupOldRates(int $retentionYears): int
    {
        $cutoffDate = now()->subYears($retentionYears)->format('Y-m-d');

        return $this->deleteBy([
            ['effective_date', '<', $cutoffDate]
        ]);
    }

    /**
     * Get unique currency pairs.
     */
    public function getUniqueCurrencyPairs(): Collection
    {
        return $this->model
            ->select('from_currency', 'to_currency')
            ->distinct()
            ->get();
    }

    /**
     * Get rates by source.
     */
    public function getRatesBySource(string $source, string $date = null): Collection
    {
        $query = $this->model->where('source', $source);

        if ($date) {
            $query->where('effective_date', $date);
        }

        return $query->get();
    }

    /**
     * Check if currency code exists in any rate.
     */
    public function currencyExists(string $currency): bool
    {
        return $this->model
            ->where(function ($q) use ($currency) {
                $q->where('from_currency', $currency)
                  ->orWhere('to_currency', $currency);
            })
            ->exists();
    }

    /**
     * Get history for currency pair, excluding specified dates.
     */
    public function getHistory(string $fromCurrency, string $toCurrency, string $startDate, string $endDate, array $excludeDates = []): Collection
    {
        $query = $this->model
            ->where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->whereBetween('effective_date', [$startDate, $endDate]);

        if (!empty($excludeDates)) {
            $query->whereNotIn('effective_date', $excludeDates);
        }

        return $query->orderBy('effective_date')->get();
    }
}
