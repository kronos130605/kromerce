<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CurrencyRateGlobal extends Model
{
    use HasFactory;

    protected $fillable = [
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
     * Get the latest rate for a currency pair.
     */
    public static function getLatestRate(string $fromCurrency, string $toCurrency): ?self
    {
        return static::where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->orderBy('effective_date', 'desc')
            ->first();
    }

    /**
     * Get rate for a specific date (or closest previous date).
     */
    public static function getRateForDate(string $fromCurrency, string $toCurrency, string $date): ?self
    {
        return static::where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->where('effective_date', '<=', $date)
            ->orderBy('effective_date', 'desc')
            ->first();
    }

    /**
     * Create or update rate for a specific date.
     */
    public static function updateRate(string $fromCurrency, string $toCurrency, float $rate, string $date, string $source = 'api'): self
    {
        return static::updateOrCreate(
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
     * Get all rates for a specific date.
     */
    public static function getAllRatesForDate(string $date): array
    {
        $rates = static::where('effective_date', $date)->get();
        
        $result = [];
        foreach ($rates as $rate) {
            $key = $rate->from_currency . '-' . $rate->to_currency;
            $result[$key] = $rate->rate;
        }
        
        return $result;
    }

    /**
     * Clean up old rates based on retention policy.
     */
    public static function cleanupOldRates(int $retentionYears = 2): int
    {
        $cutoffDate = now()->subYears($retentionYears)->format('Y-m-d');
        
        return static::where('effective_date', '<', $cutoffDate)->delete();
    }
}
