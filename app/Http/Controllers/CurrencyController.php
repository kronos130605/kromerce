<?php

namespace App\Http\Controllers;

use App\Models\BusinessCurrencyConfig;
use App\Models\CurrencyRateGlobal;
use App\Models\CurrencyRateBusiness;
use App\Models\CurrencyRateUpdate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class CurrencyController extends Controller
{
    /**
     * Display the currency configuration page.
     */
    public function index(Request $request): Response
    {
        $tenant = $request->user()->tenant;
        $currencyConfig = $tenant->currencyConfig ?? $this->createDefaultConfig($tenant);
        
        $supportedCurrencies = $currencyConfig->getSupportedCurrenciesWithRates();
        $currentRates = $this->getCurrentRates($currencyConfig);

        return Inertia::render('Currency/Index', [
            'currencyConfig' => $currencyConfig,
            'supportedCurrencies' => $supportedCurrencies,
            'currentRates' => $currentRates,
            'availableCurrencies' => $this->getAvailableCurrencies(),
        ]);
    }

    /**
     * Update currency configuration.
     */
    public function updateConfig(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'default_currency' => 'required|string|size:3',
            'display_currencies' => 'required|array|min:1',
            'display_currencies.*' => 'required|string|size:3',
            'use_custom_rates' => 'boolean',
            'auto_update_rates' => 'boolean',
            'rate_update_frequency' => 'required|in:daily,weekly,monthly',
            'historical_retention_years' => 'required|integer|min:1|max:10',
        ]);

        $tenant = $request->user()->tenant;
        
        $currencyConfig = BusinessCurrencyConfig::updateOrCreate(
            ['tenant_id' => $tenant->id],
            [
                'default_currency' => $validated['default_currency'],
                'display_currencies' => $validated['display_currencies'],
                'use_custom_rates' => $validated['use_custom_rates'],
                'auto_update_rates' => $validated['auto_update_rates'],
                'rate_update_frequency' => $validated['rate_update_frequency'],
                'historical_retention_years' => $validated['historical_retention_years'],
                'last_rate_update' => now()->format('Y-m-d'),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Currency configuration updated successfully',
            'config' => $currencyConfig,
        ]);
    }

    /**
     * Get current rates for the tenant.
     */
    public function getCurrentRates(Request $request): JsonResponse
    {
        $tenant = $request->user()->tenant;
        $currencyConfig = $tenant->currencyConfig;
        
        if (!$currencyConfig) {
            return response()->json(['error' => 'Currency configuration not found'], 404);
        }

        $rates = $this->getCurrentRates($currencyConfig);

        return response()->json([
            'rates' => $rates,
            'base_currency' => $currencyConfig->default_currency,
        ]);
    }

    /**
     * Update custom rates for the tenant.
     */
    public function updateCustomRates(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'rates' => 'required|array',
            'rates.*' => 'required|numeric|min:0',
            'effective_date' => 'required|date',
        ]);

        $tenant = $request->user()->tenant;
        $currencyConfig = $tenant->currencyConfig;
        
        if (!$currencyConfig) {
            return response()->json(['error' => 'Currency configuration not found'], 404);
        }

        $baseCurrency = $currencyConfig->default_currency;
        $targetCurrencies = $currencyConfig->display_currencies;
        $effectiveDate = $validated['effective_date'];
        $results = [];

        foreach ($targetCurrencies as $targetCurrency) {
            if ($targetCurrency === $baseCurrency) {
                continue; // Skip base currency
            }

            if (isset($validated['rates'][$targetCurrency])) {
                try {
                    $rate = CurrencyRateBusiness::updateRate(
                        $tenant->id,
                        $baseCurrency,
                        $targetCurrency,
                        $validated['rates'][$targetCurrency],
                        $effectiveDate,
                        'manual'
                    );

                    $results[] = [
                        'from_currency' => $baseCurrency,
                        'to_currency' => $targetCurrency,
                        'rate' => $validated['rates'][$targetCurrency],
                        'success' => true,
                    ];
                } catch (\Exception $e) {
                    $results[] = [
                        'from_currency' => $baseCurrency,
                        'to_currency' => $targetCurrency,
                        'rate' => $validated['rates'][$targetCurrency],
                        'success' => false,
                        'error' => $e->getMessage(),
                    ];
                }
            }
        }

        // Update last rate update date
        $currencyConfig->update(['last_rate_update' => $effectiveDate]);

        return response()->json([
            'success' => true,
            'message' => 'Custom rates updated successfully',
            'results' => $results,
        ]);
    }

    /**
     * Get rate history for a currency pair.
     */
    public function getRateHistory(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from_currency' => 'required|string|size:3',
            'to_currency' => 'required|string|size:3',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $tenant = $request->user()->tenant;
        $currencyConfig = $tenant->currencyConfig;
        
        if (!$currencyConfig) {
            return response()->json(['error' => 'Currency configuration not found'], 404);
        }

        $history = [];

        // Get custom rates first
        if ($currencyConfig->use_custom_rates) {
            $customHistory = CurrencyRateBusiness::where('tenant_id', $tenant->id)
                ->where('from_currency', $validated['from_currency'])
                ->where('to_currency', $validated['to_currency'])
                ->whereBetween('effective_date', [$validated['start_date'], $validated['end_date']])
                ->orderBy('effective_date')
                ->get();

            foreach ($customHistory as $rate) {
                $history[] = [
                    'date' => $rate->effective_date->format('Y-m-d'),
                    'rate' => $rate->rate,
                    'source' => 'business_custom',
                ];
            }
        }

        // Get global rates for dates without custom rates
        $existingDates = collect($history)->pluck('date')->toArray();
        
        $globalHistory = CurrencyRateGlobal::where('from_currency', $validated['from_currency'])
            ->where('to_currency', $validated['to_currency'])
            ->whereBetween('effective_date', [$validated['start_date'], $validated['end_date']])
            ->whereNotIn('effective_date', $existingDates)
            ->orderBy('effective_date')
            ->get();

        foreach ($globalHistory as $rate) {
            $history[] = [
                'date' => $rate->effective_date->format('Y-m-d'),
                'rate' => $rate->rate,
                'source' => 'global_default',
            ];
        }

        // Sort by date
        usort($history, function ($a, $b) {
            return strcmp($a['date'], $b['date']);
        });

        return response()->json([
            'history' => $history,
        ]);
    }

    /**
     * Get rate update summary.
     */
    public function getUpdateSummary(Request $request): JsonResponse
    {
        $summary = CurrencyRateUpdate::getRecentSummary(30);

        return response()->json($summary);
    }

    /**
     * Reset custom rates to use global rates.
     */
    public function resetToGlobal(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'currencies' => 'required|array',
            'currencies.*' => 'required|string|size:3',
        ]);

        $tenant = $request->user()->tenant;
        $currencyConfig = $tenant->currencyConfig;
        
        if (!$currencyConfig) {
            return response()->json(['error' => 'Currency configuration not found'], 404);
        }

        $baseCurrency = $currencyConfig->default_currency;
        $deleted = 0;

        foreach ($validated['currencies'] as $targetCurrency) {
            if ($targetCurrency === $baseCurrency) {
                continue;
            }

            $deleted += CurrencyRateBusiness::where('tenant_id', $tenant->id)
                ->where('from_currency', $baseCurrency)
                ->where('to_currency', $targetCurrency)
                ->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Custom rates reset successfully',
            'deleted_count' => $deleted,
        ]);
    }

    /**
     * Create default currency configuration for tenant.
     */
    private function createDefaultConfig($tenant): BusinessCurrencyConfig
    {
        return BusinessCurrencyConfig::create([
            'tenant_id' => $tenant->id,
            'default_currency' => 'USD',
            'display_currencies' => ['USD', 'EUR', 'GBP'],
            'use_custom_rates' => false,
            'auto_update_rates' => true,
            'rate_update_frequency' => 'daily',
            'historical_retention_years' => 2,
        ]);
    }

    /**
     * Get current rates for a currency configuration.
     */
    private function getCurrentRates(BusinessCurrencyConfig $config): array
    {
        $baseCurrency = $config->default_currency;
        $targetCurrencies = $config->display_currencies;
        $rates = [];

        foreach ($targetCurrencies as $currency) {
            if ($currency === $baseCurrency) {
                $rates[$currency] = [
                    'from_currency' => $baseCurrency,
                    'to_currency' => $currency,
                    'rate' => 1.0,
                    'source' => 'base',
                    'effective_date' => now()->format('Y-m-d'),
                ];
            } else {
                try {
                    $rateInfo = $config->getEffectiveRate($baseCurrency, $currency);
                    $rates[$currency] = [
                        'from_currency' => $baseCurrency,
                        'to_currency' => $currency,
                        'rate' => $rateInfo['rate'],
                        'source' => $rateInfo['source'],
                        'effective_date' => $rateInfo['effective_date'],
                    ];
                } catch (\Exception $e) {
                    $rates[$currency] = null;
                }
            }
        }

        return $rates;
    }

    /**
     * Get list of available currencies.
     */
    private function getAvailableCurrencies(): array
    {
        return [
            [
                'code' => 'USD',
                'name' => 'US Dollar',
                'symbol' => '$',
                'flag' => '🇺🇸',
            ],
            [
                'code' => 'EUR',
                'name' => 'Euro',
                'symbol' => '€',
                'flag' => '🇪🇺',
            ],
            [
                'code' => 'GBP',
                'name' => 'British Pound',
                'symbol' => '£',
                'flag' => '🇬🇧',
            ],
            [
                'code' => 'JPY',
                'name' => 'Japanese Yen',
                'symbol' => '¥',
                'flag' => '🇯🇵',
            ],
            [
                'code' => 'COP',
                'name' => 'Colombian Peso',
                'symbol' => '$',
                'flag' => '🇨🇴',
            ],
            [
                'code' => 'MXN',
                'name' => 'Mexican Peso',
                'symbol' => '$',
                'flag' => '🇲🇽',
            ],
            [
                'code' => 'CAD',
                'name' => 'Canadian Dollar',
                'symbol' => 'C$',
                'flag' => '🇨🇦',
            ],
            [
                'code' => 'AUD',
                'name' => 'Australian Dollar',
                'symbol' => 'A$',
                'flag' => '🇦🇺',
            ],
            [
                'code' => 'CHF',
                'name' => 'Swiss Franc',
                'symbol' => 'Fr',
                'flag' => '🇨🇭',
            ],
            [
                'code' => 'CNY',
                'name' => 'Chinese Yuan',
                'symbol' => '¥',
                'flag' => '🇨🇳',
            ],
            [
                'code' => 'INR',
                'name' => 'Indian Rupee',
                'symbol' => '₹',
                'flag' => '🇮🇳',
            ],
        ];
    }
}
