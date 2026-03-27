<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CurrencyRateBusiness extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'from_currency',
        'to_currency',
        'rate',
        'effective_date',
        'source',
    ];

    protected $casts = [
        'rate' => 'decimal:6',
        'effective_date' => 'date',
    ];

    /**
     * Get the store that owns the custom rate.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the latest custom rate for a store and currency pair.
     */
    public static function getLatestRate(string $storeId, string $fromCurrency, string $toCurrency): ?self
    {
        return static::where('store_id', $storeId)
            ->where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->orderBy('effective_date', 'desc')
            ->first();
    }

    /**
     * Get custom rate for a specific date (or closest previous date).
     */
    public static function getRateForDate(string $storeId, string $fromCurrency, string $toCurrency, string $date): ?self
    {
        return static::where('store_id', $storeId)
            ->where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->where('effective_date', '<=', $date)
            ->orderBy('effective_date', 'desc')
            ->first();
    }

    /**
     * Create or update custom rate for a specific date.
     */
    public static function updateRate(string $storeId, string $fromCurrency, string $toCurrency, float $rate, string $date, string $source = 'manual'): self
    {
        return static::updateOrCreate(
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
     * Get all custom rates for a store on a specific date.
     */
    public static function getAllRatesForStore(string $storeId, string $date): array
    {
        $rates = static::where('store_id', $storeId)
            ->where('effective_date', $date)
            ->get();

        $result = [];
        foreach ($rates as $rate) {
            $key = $rate->from_currency . '-' . $rate->to_currency;
            $result[$key] = $rate->rate;
        }

        return $result;
    }

    /**
     * Clean up old rates for a store based on retention policy.
     */
    public static function cleanupOldRates(string $storeId, int $retentionYears = 2): int
    {
        $cutoffDate = now()->subYears($retentionYears)->format('Y-m-d');

        return static::where('store_id', $storeId)
            ->where('effective_date', '<', $cutoffDate)
            ->delete();
    }

    /**
     * Batch update rates for a store.
     */
    public static function batchUpdateRates(string $storeId, array $rates, string $date, string $source = 'manual'): array
    {
        $results = [];

        foreach ($rates as $fromCurrency => $toCurrencies) {
            foreach ($toCurrencies as $toCurrency => $rate) {
                try {
                    $updatedRate = static::updateRate($storeId, $fromCurrency, $toCurrency, $rate, $date, $source);
                    $results[] = [
                        'from_currency' => $fromCurrency,
                        'to_currency' => $toCurrency,
                        'rate' => $rate,
                        'success' => true
                    ];
                } catch (\Exception $e) {
                    $results[] = [
                        'from_currency' => $fromCurrency,
                        'to_currency' => $toCurrency,
                        'rate' => $rate,
                        'success' => false,
                        'error' => $e->getMessage()
                    ];
                }
            }
        }

        return $results;
    }
}
