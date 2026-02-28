<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessCurrencyConfig extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'default_currency',
        'display_currencies',
        'use_custom_rates',
        'auto_update_rates',
        'rate_update_frequency',
        'last_rate_update',
        'historical_retention_years',
    ];

    protected $casts = [
        'display_currencies' => 'array',
        'use_custom_rates' => 'boolean',
        'auto_update_rates' => 'boolean',
        'last_rate_update' => 'date',
        'historical_retention_years' => 'integer',
    ];

    /**
     * Get the tenant that owns the currency config.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the custom rates for this business.
     */
    public function customRates(): HasMany
    {
        return $this->hasMany(CurrencyRateBusiness::class, 'tenant_id', 'tenant_id');
    }

    /**
     * Get all supported currencies with their current rates.
     * Note: This method delegates to CurrencyRateService for clean architecture.
     */
    public function getSupportedCurrenciesWithRates(): array
    {
        // Delegar al service para mantener arquitectura limpia
        return app(\App\Services\CurrencyRateService::class)->getSupportedCurrenciesWithRates($this->tenant_id);
    }

    /**
     * Get currency symbol.
     */
    private function getCurrencySymbol(string $currency): string
    {
        $symbols = [
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
            'COP' => '$',
            'MXN' => '$',
            'CAD' => 'C$',
            'AUD' => 'A$',
            'CHF' => 'Fr',
            'CNY' => '¥',
            'INR' => '₹',
        ];

        return $symbols[$currency] ?? $currency;
    }

    /**
     * Get currency name.
     */
    private function getCurrencyName(string $currency): string
    {
        $names = [
            'USD' => 'US Dollar',
            'EUR' => 'Euro',
            'GBP' => 'British Pound',
            'JPY' => 'Japanese Yen',
            'COP' => 'Colombian Peso',
            'MXN' => 'Mexican Peso',
            'CAD' => 'Canadian Dollar',
            'AUD' => 'Australian Dollar',
            'CHF' => 'Swiss Franc',
            'CNY' => 'Chinese Yuan',
            'INR' => 'Indian Rupee',
        ];

        return $names[$currency] ?? $currency;
    }

    /**
     * Get currency flag emoji.
     */
    private function getCurrencyFlag(string $currency): string
    {
        $flags = [
            'USD' => '🇺🇸',
            'EUR' => '🇪🇺',
            'GBP' => '🇬🇧',
            'JPY' => '🇯🇵',
            'COP' => '🇨🇴',
            'MXN' => '🇲🇽',
            'CAD' => '🇨🇦',
            'AUD' => '🇦🇺',
            'CHF' => '🇨🇭',
            'CNY' => '🇨🇳',
            'INR' => '🇮🇳',
        ];

        return $flags[$currency] ?? '🌍';
    }
}
