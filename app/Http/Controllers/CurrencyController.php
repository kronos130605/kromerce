<?php

namespace App\Http\Controllers;

use App\Services\CurrencyRateService;
use App\Repositories\BusinessCurrencyConfigRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class CurrencyController extends Controller
{
    private CurrencyRateService $currencyService;
    private BusinessCurrencyConfigRepository $configRepo;

    public function __construct(
        CurrencyRateService $currencyService,
        BusinessCurrencyConfigRepository $configRepo
    ) {
        $this->currencyService = $currencyService;
        $this->configRepo = $configRepo;
    }
    /**
     * Display the currency configuration page.
     */
    public function index(Request $request): Response
    {
        $tenant = $request->user()->tenant;
        $currencyConfig = $this->configRepo->getOrCreateForTenant($tenant->id);

        return Inertia::render('modules/dashboard/DashboardCurrency', [
            'currencyConfig' => $currencyConfig,
            'supportedCurrencies' => $currencyConfig->getSupportedCurrenciesWithRates(),
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
        
        $currencyConfig = $this->configRepo->updateOrCreate(
            ['tenant_id' => $tenant->id],
            array_merge($validated, [
                'last_rate_update' => now()->format('Y-m-d'),
            ])
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
        $currencyConfig = $this->configRepo->getByTenantId($tenant->id);

        if (!$currencyConfig) {
            return response()->json(['error' => 'Currency configuration not found'], 404);
        }

        return response()->json([
            'rates' => $this->getCurrentRatesArray($currencyConfig),
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
        
        try {
            $results = $this->currencyService->updateCustomRates(
                $tenant->id,
                $validated['rates'],
                $validated['effective_date']
            );

            return response()->json([
                'success' => true,
                'message' => 'Custom rates updated successfully',
                'results' => $results,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
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
        
        try {
            $history = $this->currencyService->getRateHistory(
                $validated['from_currency'],
                $validated['to_currency'],
                $validated['start_date'],
                $validated['end_date'],
                $tenant->id
            );

            return response()->json(['history' => $history]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get rate update summary.
     */
    public function getUpdateSummary(Request $request): JsonResponse
    {
        $summary = $this->currencyService->getUpdateSummary(30);

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
        
        try {
            $deletedCount = $this->currencyService->resetToGlobal($tenant->id, $validated['currencies']);

            return response()->json([
                'success' => true,
                'message' => 'Custom rates reset successfully',
                'deleted_count' => $deletedCount,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get current rates array for configuration.
     */
    private function getCurrentRatesArray($config): array
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
                    $rateInfo = $this->currencyService->getEffectiveRate($config->tenant_id, $baseCurrency, $currency);
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
