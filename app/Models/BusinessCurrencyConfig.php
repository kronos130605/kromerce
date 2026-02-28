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
     * Get effective rate for a currency pair.
     */
    public function getEffectiveRate(string $fromCurrency, string $toCurrency, ?string $date = null): array
    {
        $targetDate = $date ?? now()->format('Y-m-d');
        
        // Try custom rate first
        if ($this->use_custom_rates) {
            $customRate = CurrencyRateBusiness::where('tenant_id', $this->tenant_id)
                ->where('from_currency', $fromCurrency)
                ->where('to_currency', $toCurrency)
                ->where('effective_date', '<=', $targetDate)
                ->orderBy('effective_date', 'desc')
                ->first();

            if ($customRate) {
                return [
                    'rate' => $customRate->rate,
                    'source' => 'business_custom',
                    'effective_date' => $customRate->effective_date->format('Y-m-d')
                ];
            }
        }

        // Fallback to global rate
        $globalRate = CurrencyRateGlobal::where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->where('effective_date', '<=', $targetDate)
            ->orderBy('effective_date', 'desc')
            ->first();

        if ($globalRate) {
            return [
                'rate' => $globalRate->rate,
                'source' => 'global_default',
                'effective_date' => $globalRate->effective_date->format('Y-m-d')
            ];
        }

        // Last resort: throw exception or return null
        throw new \Exception("No rate found for {$fromCurrency} to {$toCurrency} on {$targetDate}");
    }

    /**
     * Get all supported currencies with their current rates.
     */
    public function getSupportedCurrenciesWithRates(): array
    {
        $currencies = [];
        $baseCurrency = $this->default_currency;

        foreach ($this->display_currencies as $currency) {
            if ($currency === $baseCurrency) {
                $currencies[$currency] = [
                    'code' => $currency,
                    'rate' => 1.0,
                    'symbol' => $this->getCurrencySymbol($currency),
                    'name' => $this->getCurrencyName($currency),
                    'flag' => $this->getCurrencyFlag($currency),
                    'source' => 'base'
                ];
            } else {
                try {
                    $rateInfo = $this->getEffectiveRate($baseCurrency, $currency);
                    $currencies[$currency] = [
                        'code' => $currency,
                        'rate' => $rateInfo['rate'],
                        'symbol' => $this->getCurrencySymbol($currency),
                        'name' => $this->getCurrencyName($currency),
                        'flag' => $this->getCurrencyFlag($currency),
                        'source' => $rateInfo['source']
                    ];
                } catch (\Exception $e) {
                    // Skip currencies without rates
                    continue;
                }
            }
        }

        return $currencies;
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
