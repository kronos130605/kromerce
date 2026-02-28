<?php

namespace App\Services;

use App\Repositories\BusinessCurrencyConfigRepository;
use App\Repositories\CurrencyRateGlobalRepository;
use App\Repositories\CurrencyRateBusinessRepository;
use App\Repositories\CurrencyRateUpdateRepository;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CurrencyRateService
{
    private BusinessCurrencyConfigRepository $configRepo;
    private CurrencyRateGlobalRepository $globalRateRepo;
    private CurrencyRateBusinessRepository $businessRateRepo;
    private CurrencyRateUpdateRepository $updateRepo;

    public function __construct(
        BusinessCurrencyConfigRepository $configRepo,
        CurrencyRateGlobalRepository $globalRateRepo,
        CurrencyRateBusinessRepository $businessRateRepo,
        CurrencyRateUpdateRepository $updateRepo
    ) {
        $this->configRepo = $configRepo;
        $this->globalRateRepo = $globalRateRepo;
        $this->businessRateRepo = $businessRateRepo;
        $this->updateRepo = $updateRepo;
    }
    /**
     * Update daily currency rates.
     */
    public function updateDailyRates(): array
    {
        $today = now()->format('Y-m-d');
        $currencies = ['USD', 'EUR', 'GBP', 'JPY', 'COP', 'MXN', 'CAD', 'AUD', 'CHF', 'CNY', 'INR'];
        $results = [];
        $totalUpdated = 0;

        try {
            // Update global rates
            $globalResults = $this->updateGlobalRates($currencies, $today);
            $results['global'] = $globalResults;
            $totalUpdated += $globalResults['updated'];

            // Update business rates for auto-update enabled businesses
            $businessResults = $this->updateBusinessRates($today);
            $results['business'] = $businessResults;
            $totalUpdated += $businessResults['updated'];

            // Clean up old rates
            $this->cleanupOldRates();

            // Log successful update
            $this->updateRepo->createSuccess(
                array_merge($globalResults['currencies'], $businessResults['currencies']),
                'api',
                $totalUpdated
            );

            return [
                'success' => true,
                'total_updated' => $totalUpdated,
                'results' => $results,
            ];

        } catch (\Exception $e) {
            Log::error('Currency rate update failed', [
                'date' => $today,
                'error' => $e->getMessage(),
            ]);

            // Log failed update
            $this->updateRepo->createFailure(
                array_merge($globalResults['currencies'] ?? [], $businessResults['currencies'] ?? []),
                'api',
                $e->getMessage()
            );

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'results' => $results ?? [],
            ];
        }
    }

    /**
     * Update global currency rates.
     */
    private function updateGlobalRates(array $currencies, string $date): array
    {
        $updated = 0;
        $currencyPairs = [];

        foreach ($currencies as $fromCurrency) {
            foreach ($currencies as $toCurrency) {
                if ($fromCurrency === $toCurrency) {
                    continue;
                }

                try {
                    $rate = $this->fetchRateFromAPI($fromCurrency, $toCurrency);
                    
                    if ($rate) {
                        $this->globalRateRepo->updateOrCreateRate($fromCurrency, $toCurrency, $rate, $date, 'api');
                        $updated++;
                        $currencyPairs[] = "{$fromCurrency}-{$toCurrency}";
                    }
                } catch (\Exception $e) {
                    // Try to use previous day's rate as fallback
                    $previousRate = $this->getPreviousDayRate($fromCurrency, $toCurrency, $date);
                    if ($previousRate) {
                        $this->globalRateRepo->updateOrCreateRate($fromCurrency, $toCurrency, $previousRate, $date, 'previous_day');
                        $updated++;
                        $currencyPairs[] = "{$fromCurrency}-{$toCurrency}";
                    }
                }
            }
        }

        return [
            'updated' => $updated,
            'currencies' => $currencyPairs,
        ];
    }

    /**
     * Update business rates for auto-update enabled businesses.
     */
    private function updateBusinessRates(string $date): array
    {
        $updated = 0;
        $currencyPairs = [];

        $businesses = $this->configRepo->getNeedingRateUpdate();

        foreach ($businesses as $config) {
            $baseCurrency = $config->default_currency;
            $targetCurrencies = $config->display_currencies;

            foreach ($targetCurrencies as $targetCurrency) {
                if ($targetCurrency === $baseCurrency) {
                    continue;
                }

                try {
                    $rate = $this->fetchRateFromAPI($baseCurrency, $targetCurrency);
                    
                    if ($rate) {
                        $this->businessRateRepo->updateOrCreateRate(
                            $config->tenant_id,
                            $baseCurrency,
                            $targetCurrency,
                            $rate,
                            $date,
                            'api'
                        );
                        $updated++;
                        $currencyPairs[] = "{$baseCurrency}-{$targetCurrency}";
                    }
                } catch (\Exception $e) {
                    // Use global rate as fallback
                    $globalRate = $this->globalRateRepo->getRateForDate($baseCurrency, $targetCurrency, $date);
                    if ($globalRate) {
                        $this->businessRateRepo->updateOrCreateRate(
                            $config->tenant_id,
                            $baseCurrency,
                            $targetCurrency,
                            $globalRate->rate,
                            $date,
                            'global_fallback'
                        );
                        $updated++;
                        $currencyPairs[] = "{$baseCurrency}-{$targetCurrency}";
                    }
                }
            }

            // Update last rate update date
            $this->configRepo->updateForTenant($config->tenant_id, ['last_rate_update' => $date]);
        }

        return [
            'updated' => $updated,
            'currencies' => $currencyPairs,
        ];
    }

    /**
     * Fetch rate from API.
     */
    private function fetchRateFromAPI(string $fromCurrency, string $toCurrency): ?float
    {
        // For now, using a mock implementation
        // In production, integrate with real API like OpenExchangeRates, CurrencyLayer, etc.
        
        if ($fromCurrency === $toCurrency) {
            return 1.0;
        }

        // Mock rates for development
        $mockRates = $this->getMockRates();
        $key = "{$fromCurrency}-{$toCurrency}";
        
        if (isset($mockRates[$key])) {
            return $mockRates[$key];
        }

        // Try inverse rate
        $inverseKey = "{$toCurrency}-{$fromCurrency}";
        if (isset($mockRates[$inverseKey])) {
            return 1 / $mockRates[$inverseKey];
        }

        // Default to USD conversion
        if ($fromCurrency === 'USD') {
            $usdRates = [
                'EUR' => 0.92,
                'GBP' => 0.79,
                'JPY' => 149.50,
                'COP' => 4000.0,
                'MXN' => 17.15,
                'CAD' => 1.36,
                'AUD' => 1.53,
                'CHF' => 0.87,
                'CNY' => 7.24,
                'INR' => 83.12,
            ];
            
            return $usdRates[$toCurrency] ?? null;
        }

        return null;
    }

    /**
     * Get mock rates for development.
     */
    private function getMockRates(): array
    {
        return [
            'USD-EUR' => 0.92,
            'USD-GBP' => 0.79,
            'USD-JPY' => 149.50,
            'USD-COP' => 4000.0,
            'USD-MXN' => 17.15,
            'USD-CAD' => 1.36,
            'USD-AUD' => 1.53,
            'USD-CHF' => 0.87,
            'USD-CNY' => 7.24,
            'USD-INR' => 83.12,
            'EUR-USD' => 1.09,
            'EUR-GBP' => 0.86,
            'EUR-JPY' => 162.89,
            'EUR-COP' => 4347.83,
            'EUR-MXN' => 18.64,
            'EUR-CAD' => 1.48,
            'EUR-AUD' => 1.66,
            'EUR-CHF' => 0.95,
            'EUR-CNY' => 7.87,
            'EUR-INR' => 90.35,
            'GBP-USD' => 1.27,
            'GBP-EUR' => 1.16,
            'GBP-JPY' => 189.37,
            'GBP-COP' => 5063.29,
            'GBP-MXN' => 21.71,
            'GBP-CAD' => 1.72,
            'GBP-AUD' => 1.94,
            'GBP-CHF' => 1.10,
            'GBP-CNY' => 9.16,
            'GBP-INR' => 105.20,
        ];
    }

    /**
     * Get previous day's rate as fallback.
     */
    private function getPreviousDayRate(string $fromCurrency, string $toCurrency, string $date): ?float
    {
        $previousDate = Carbon::parse($date)->subDay()->format('Y-m-d');
        
        $rate = $this->globalRateRepo->getRateForDate($fromCurrency, $toCurrency, $previousDate);
        
        return $rate ? $rate->rate : null;
    }

    /**
     * Clean up old rates based on retention policies.
     */
    private function cleanupOldRates(): void
    {
        // Clean global rates (keep 2 years)
        $this->globalRateRepo->cleanupOldRates(2);

        // Clean business rates based on their retention settings
        $configs = $this->configRepo->getAll();
        
        foreach ($configs as $config) {
            $this->businessRateRepo->cleanupOldRates($config->tenant_id, $config->historical_retention_years);
        }
    }

    /**
     * Get effective rate for a tenant.
     */
    public function getEffectiveRate(string $tenantId, string $fromCurrency, string $toCurrency, ?string $date = null): array
    {
        $config = $this->configRepo->getByTenantId($tenantId);
        
        if (!$config) {
            throw new \Exception("Currency configuration not found for tenant: {$tenantId}");
        }

        // Try business custom rate first
        if ($config->use_custom_rates) {
            $businessRate = $this->businessRateRepo->getRateForDate($tenantId, $fromCurrency, $toCurrency, $date);
            
            if ($businessRate) {
                return [
                    'rate' => $businessRate->rate,
                    'source' => 'business_custom',
                    'effective_date' => $businessRate->effective_date->format('Y-m-d'),
                ];
            }
        }

        // Fallback to global rate
        $globalRate = $this->globalRateRepo->getRateForDate($fromCurrency, $toCurrency, $date);
        
        if ($globalRate) {
            return [
                'rate' => $globalRate->rate,
                'source' => 'global_default',
                'effective_date' => $globalRate->effective_date->format('Y-m-d'),
            ];
        }

        throw new \Exception("Rate not found for {$fromCurrency} to {$toCurrency}");
    }

    /**
     * Calculate price in target currency.
     */
    public function calculatePrice(float $amount, string $fromCurrency, string $toCurrency, string $tenantId, ?string $date = null): array
    {
        if ($fromCurrency === $toCurrency) {
            return [
                'amount' => $amount,
                'rate' => 1.0,
                'source' => 'same_currency',
            ];
        }

        $rateInfo = $this->getEffectiveRate($tenantId, $fromCurrency, $toCurrency, $date);
        
        return [
            'amount' => round($amount * $rateInfo['rate'], 2),
            'rate' => $rateInfo['rate'],
            'source' => $rateInfo['source'],
            'effective_date' => $rateInfo['effective_date'],
        ];
    }

    /**
     * Get rate history for analysis.
     */
    public function getRateHistory(string $fromCurrency, string $toCurrency, string $startDate, string $endDate, ?string $tenantId = null): array
    {
        $history = [];

        if ($tenantId) {
            $config = BusinessCurrencyConfig::where('tenant_id', $tenantId)->first();
            
            if ($config && $config->use_custom_rates) {
                $customHistory = CurrencyRateBusiness::where('tenant_id', $tenantId)
                    ->where('from_currency', $fromCurrency)
                    ->where('to_currency', $toCurrency)
                    ->whereBetween('effective_date', [$startDate, $endDate])
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
        }

        // Get global rates for missing dates
        $existingDates = collect($history)->pluck('date')->toArray();
        
        $globalHistory = CurrencyRateGlobal::where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->whereBetween('effective_date', [$startDate, $endDate])
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

        return $history;
    }
}
