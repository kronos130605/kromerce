<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CurrencyRateUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'update_date',
        'currencies_updated',
        'source',
        'success',
        'error_message',
        'total_rates_updated',
    ];

    protected $casts = [
        'update_date' => 'date',
        'currencies_updated' => 'array',
        'success' => 'boolean',
        'total_rates_updated' => 'integer',
    ];

    /**
     * Get the formatted currencies updated list.
     */
    public function getFormattedCurrenciesUpdatedAttribute(): string
    {
        if (!$this->currencies_updated) {
            return 'None';
        }

        return implode(', ', $this->currencies_updated);
    }

    /**
     * Scope to get successful updates.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('success', true);
    }

    /**
     * Scope to get failed updates.
     */
    public function scopeFailed($query)
    {
        return $query->where('success', false);
    }

    /**
     * Scope to get updates by source.
     */
    public function scopeSource($query, string $source)
    {
        return $query->where('source', $source);
    }

    /**
     * Scope to get updates in date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('update_date', [$startDate, $endDate]);
    }

    /**
     * Create a successful update record.
     */
    public static function createSuccess(array $currenciesUpdated, string $source = 'api', int $totalRates = 0): self
    {
        return static::create([
            'update_date' => now()->format('Y-m-d'),
            'currencies_updated' => $currenciesUpdated,
            'source' => $source,
            'success' => true,
            'total_rates_updated' => $totalRates,
        ]);
    }

    /**
     * Create a failed update record.
     */
    public static function createFailure(array $currenciesUpdated, string $source, string $errorMessage): self
    {
        return static::create([
            'update_date' => now()->format('Y-m-d'),
            'currencies_updated' => $currenciesUpdated,
            'source' => $source,
            'success' => false,
            'error_message' => $errorMessage,
            'total_rates_updated' => 0,
        ]);
    }

    /**
     * Get recent updates summary.
     */
    public static function getRecentSummary(int $days = 7): array
    {
        $startDate = now()->subDays($days);
        
        $updates = static::where('update_date', '>=', $startDate)
            ->orderBy('update_date', 'desc')
            ->get();

        return [
            'total_updates' => $updates->count(),
            'successful_updates' => $updates->where('success', true)->count(),
            'failed_updates' => $updates->where('success', false)->count(),
            'last_update' => $updates->first(),
            'success_rate' => $updates->count() > 0 
                ? round(($updates->where('success', true)->count() / $updates->count()) * 100, 2)
                : 0,
        ];
    }
}
