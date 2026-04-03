<?php

namespace App\Services;

use App\Models\CurrencySource;
use App\Repositories\Store\BusinessCurrencyConfigRepository;
use App\Repositories\Store\CurrencySourceRepository;
use App\Repositories\Currency\CurrencyRateGlobalRepository;
use App\Repositories\Currency\CurrencyRateUpdateRepository;
use App\Services\Currency\CurrencyProviderFactory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CurrencyRateService
{
    private BusinessCurrencyConfigRepository $configRepo;
    private CurrencySourceRepository $sourceRepo;
    private CurrencyRateGlobalRepository $globalRateRepo;
    private CurrencyRateUpdateRepository $updateRepo;

    public function __construct(
        BusinessCurrencyConfigRepository $configRepo,
        CurrencySourceRepository $sourceRepo,
        CurrencyRateGlobalRepository $globalRateRepo,
        CurrencyRateUpdateRepository $updateRepo
    ) {
        $this->configRepo = $configRepo;
        $this->sourceRepo = $sourceRepo;
        $this->globalRateRepo = $globalRateRepo;
        $this->updateRepo = $updateRepo;
    }
    /**
     * Update daily currency rates.
     */
    public function updateDailyRates(): array
    {
        $today = now()->format('Y-m-d');
        $results = [];
        $totalUpdated = 0;

        try {
            // Update global rates using default source
            $globalSource = $this->sourceRepo->getGlobalDefault();
            if ($globalSource) {
                $globalResults = $this->updateGlobalRatesFromSource($globalSource, $today);
                $results['global'] = $globalResults;
                $totalUpdated += $globalResults['updated'];
            }

            // Update business rates for each store using their configured source
            $businessResults = $this->updateBusinessRatesFromSources($today);
            $results['business'] = $businessResults;
            $totalUpdated += $businessResults['updated'];

            // Log successful update
            $this->updateRepo->createSuccess(
                array_merge(
                    $globalResults['currencies'] ?? [],
                    $businessResults['currencies'] ?? []
                ),
                'provider_sources',
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
                array_merge(
                    $globalResults['currencies'] ?? [],
                    $businessResults['currencies'] ?? []
                ),
                'provider_sources',
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
     * Update global rates from a source.
     */
    private function updateGlobalRatesFromSource(CurrencySource $source, string $date): array
    {
        $currencies = array_keys(config('currencies.supported', []));
        $updated = 0;
        $currencyPairs = [];

        try {
            $provider = CurrencyProviderFactory::make($source);

            // Prepare currency pairs
            $pairs = [];
            foreach ($currencies as $from) {
                foreach ($currencies as $to) {
                    if ($from !== $to && $provider->supportsCurrency($from) && $provider->supportsCurrency($to)) {
                        $pairs[] = ['from' => $from, 'to' => $to];
                    }
                }
            }

            if (empty($pairs)) {
                return ['updated' => 0, 'currencies' => []];
            }

            // Fetch rates from provider
            $rates = $provider->fetchRates($pairs);

            Log::debug('Rates fetched from provider', [
                'source' => $source->name,
                'pairs_requested' => count($pairs),
                'rates_received' => count($rates),
                'sample_rates' => array_slice(array_keys($rates), 0, 3),
            ]);

            // Save rates
            foreach ($rates as $from => $tos) {
                foreach ($tos as $to => $rate) {
                    Log::debug('Saving rate', ['from' => $from, 'to' => $to, 'rate' => $rate]);
                    $this->globalRateRepo->updateOrCreateRate($from, $to, $rate, $date, $source->name);
                    $updated++;
                    $currencyPairs[] = "{$from}-{$to}";
                }
            }

            Log::info('Global rates updated', [
                'source' => $source->name,
                'updated_count' => $updated,
            ]);

            // Record success
            $source->recordSuccess();

        } catch (\Exception $e) {
            Log::error('Failed to update global rates from source', [
                'source' => $source->name,
                'error' => $e->getMessage(),
            ]);
            $source->recordFailure($e->getMessage());
        }

        return [
            'updated' => $updated,
            'currencies' => $currencyPairs,
        ];
    }

    /**
     * Update rates from all active sources.
     */
    private function updateAllSources(string $date): array
    {
        $totalUpdated = 0;
        $results = [];

        // Get all active sources
        $sources = $this->sourceRepo->getBy(['is_active' => true]);

        foreach ($sources as $source) {
            try {
                $result = $this->updateAllSources($date);
                $totalUpdated += $result['updated'];
                $results[$source->name] = $result;
            } catch (\Exception $e) {
                Log::error('Failed to update from source', [
                    'source' => $source->name,
                    'error' => $e->getMessage(),
                ]);
                $results[$source->name] = [
                    'updated' => 0,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'total_updated' => $totalUpdated,
            'results' => $results,
        ];
    }

    /**
     * Update business rates from each store's configured source.
     */
    private function updateBusinessRatesFromSources(string $date): array
    {
        $updated = 0;
        $currencyPairs = [];

        $businesses = $this->configRepo->getNeedingRateUpdate();

        foreach ($businesses as $config) {
            try {
                if ($config->source_id) {
                    // Use store's custom source
                    $result = $this->updateStoreRatesFromSource($config, $date);
                } else {
                    // Use global rates (copy to business rates)
                    $result = $this->updateStoreRatesFromGlobal($config, $date);
                }

                $updated += $result['updated'];
                $currencyPairs = array_merge($currencyPairs, $result['currencies']);

                // Update last rate update date
                $this->configRepo->updateBy(['store_id' => $config->store_id], ['last_rate_update' => $date]);

            } catch (\Exception $e) {
                Log::error('Failed to update business rates', [
                    'store_id' => $config->store_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return [
            'updated' => $updated,
            'currencies' => $currencyPairs,
        ];
    }

    /**
     * Update store rates from its configured source.
     */
    private function updateStoreRatesFromSource($config, string $date): array
    {
        $source = $this->sourceRepo->getFirstBy(['id' => $config->source_id]);

        if (!$source || !$source->is_active) {
            // Fallback to global
            return $this->updateStoreRatesFromGlobal($config, $date);
        }

        $updated = 0;
        $currencyPairs = [];
        $baseCurrency = $config->default_currency;
        $targetCurrencies = $config->display_currencies;

        try {
            $provider = CurrencyProviderFactory::make($source, $config->source_config_override);

            // Prepare pairs
            $pairs = [];
            foreach ($targetCurrencies as $target) {
                if ($target !== $baseCurrency) {
                    $pairs[] = ['from' => $baseCurrency, 'to' => $target];
                }
            }

            if (empty($pairs)) {
                return ['updated' => 0, 'currencies' => []];
            }

            // Fetch rates
            $rates = $provider->fetchRates($pairs);

            // Save rates to global table
            foreach ($rates as $from => $tos) {
                foreach ($tos as $to => $rate) {
                    $this->globalRateRepo->updateOrCreateRate($from, $to, $rate, $date, $source->name);
                    $updated++;
                    $currencyPairs[] = "{$from}-{$to}";
                }
            }

            $source->recordSuccess();

        } catch (\Exception $e) {
            Log::error('Provider failed for store, falling back to global', [
                'store_id' => $config->store_id,
                'source' => $source->name,
                'error' => $e->getMessage(),
            ]);

            $source->recordFailure($e->getMessage());

            // Fallback to global rates
            return $this->updateStoreRatesFromGlobal($config, $date);
        }

        return [
            'updated' => $updated,
            'currencies' => $currencyPairs,
        ];
    }

    /**
     * Update store rates from global rates (fallback).
     */
    private function updateStoreRatesFromGlobal($config, string $date): array
    {
        $updated = 0;
        $currencyPairs = [];
        $baseCurrency = $config->default_currency;
        $targetCurrencies = $config->display_currencies;

        foreach ($targetCurrencies as $targetCurrency) {
            if ($targetCurrency === $baseCurrency) {
                continue;
            }

            $globalRate = $this->globalRateRepo->getRateForDate($baseCurrency, $targetCurrency, $date);

            if ($globalRate) {
                // Rate already exists in global table, no need to duplicate
                $updated++;
                $currencyPairs[] = "{$baseCurrency}-{$targetCurrency}";
            }
        }

        return [
            'updated' => $updated,
            'currencies' => $currencyPairs,
        ];
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
     * Test a currency source connection.
     */
    public function testSourceConnection(string $sourceId, ?array $configOverride = null): array
    {
        $source = $this->sourceRepo->getFirstBy(['id' => $sourceId]);

        if (!$source) {
            return [
                'success' => false,
                'message' => 'Source not found',
            ];
        }

        return CurrencyProviderFactory::testSource($source, $configOverride);
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
    }

    /**
     * Get effective rate for a store with currency-aware source selection.
     *
     * Logic:
     * - Conversions involving CUP or CLA: Use store's configured Cuba source (BCC or ElToque)
     * - Conversions NOT involving CUP/CLA: Use global international source (OpenExchangeRates)
     * - CLA (Tarjeta Clasica) is treated as equivalent to USD
     */
    public function getEffectiveRate(string $storeId, string $fromCurrency, string $toCurrency, ?string $date = null): array
    {
        $config = $this->configRepo->getFirstBy(['store_id' => $storeId]);

        if (!$config) {
            throw new \Exception("Currency configuration not found for store: {$storeId}");
        }

        // Normalize currencies: CLA = USD
        $fromCurrency = $this->normalizeCurrency($fromCurrency);
        $toCurrency = $this->normalizeCurrency($toCurrency);

        // Determine which source to use based on currencies involved
        if ($this->isCubanCurrencyConversion($fromCurrency, $toCurrency)) {
            // Use store's Cuba source for CUP-related conversions
            return $this->getCubaRate($storeId, $config, $fromCurrency, $toCurrency, $date);
        }

        // Use international global source for non-CUP conversions
        return $this->getInternationalRate($fromCurrency, $toCurrency, $date);
    }

    /**
     * Check if a currency is a Cuban currency (CUP or CLA).
     */
    private function isCubanCurrency(string $currency): bool
    {
        return in_array(strtoupper($currency), ['CUP', 'CLA']);
    }

    /**
     * Check if conversion involves any Cuban currency.
     */
    private function isCubanCurrencyConversion(string $from, string $to): bool
    {
        return $this->isCubanCurrency($from) || $this->isCubanCurrency($to);
    }

    /**
     * Normalize currency code.
     * CLA (Tarjeta Clasica) is treated as USD.
     */
    private function normalizeCurrency(string $currency): string
    {
        $currency = strtoupper($currency);
        return $currency === 'CLA' ? 'USD' : $currency;
    }

    /**
     * Get rate for Cuba-related conversion (using store's configured Cuba source).
     */
    private function getCubaRate(string $storeId, $config, string $from, string $to, ?string $date): array
    {
        // Use preferred Cuba source from store config
        $preferredSourceId = $config->preferred_cuba_source_id ?? $config->source_id;

        if ($preferredSourceId) {
            $source = $this->sourceRepo->getFirstBy(['id' => $preferredSourceId]);

            if ($source && $source->is_active && $this->isCubaSource($source)) {
                // Try to get rate from global rates filtered by source
                $rate = $this->globalRateRepo->getRateForDateBySource($from, $to, $date ?? now()->format('Y-m-d'), $source->name);

                if ($rate) {
                    return [
                        'rate' => $rate->rate,
                        'source' => $source->name,
                        'effective_date' => $rate->effective_date->format('Y-m-d'),
                        'source_type' => 'store_cuba',
                    ];
                }
            }
        }

        // Fallback to any available Cuba rate in global table
        $globalRate = $this->globalRateRepo->getRateForDate($from, $to, $date);

        if ($globalRate) {
            return [
                'rate' => $globalRate->rate,
                'source' => $globalRate->source ?? 'global_fallback',
                'effective_date' => $globalRate->effective_date->format('Y-m-d'),
                'source_type' => 'fallback',
            ];
        }

        throw new \Exception("Rate not found for {$from} to {$to} (Cuba conversion)");
    }

    /**
     * Check if a source is a Cuba-specific source.
     */
    private function isCubaSource(CurrencySource $source): bool
    {
        $cubaScopes = ['cuba-official', 'cuba-informal'];
        $sourceScope = $source->config['scope'] ?? null;

        return in_array($sourceScope, $cubaScopes) ||
               in_array($source->code, ['bcc-cuba', 'eltoque-cuba']);
    }

    /**
     * Get rate for international conversion (using global OpenExchangeRates).
     */
    private function getInternationalRate(string $from, string $to, ?string $date): array
    {
        $globalSource = $this->sourceRepo->findByCode('openexchange-global');

        if (!$globalSource || !$globalSource->is_active) {
            // Fallback to any global rate
            $globalRate = $this->globalRateRepo->getRateForDate($from, $to, $date);

            if ($globalRate) {
                return [
                    'rate' => $globalRate->rate,
                    'source' => $globalRate->source ?? 'global',
                    'effective_date' => $globalRate->effective_date->format('Y-m-d'),
                    'source_type' => 'global_fallback',
                ];
            }

            throw new \Exception("Rate not found for {$from} to {$to} (international conversion)");
        }

        // Get rate from global rates
        $globalRate = $this->globalRateRepo->getRateForDate($from, $to, $date);

        if ($globalRate) {
            return [
                'rate' => $globalRate->rate,
                'source' => $globalSource->name,
                'effective_date' => $globalRate->effective_date->format('Y-m-d'),
                'source_type' => 'international_global',
            ];
        }

        throw new \Exception("Rate not found for {$from} to {$to} (international conversion)");
    }

    /**
     * Get supported currencies with their current rates for a store.
     */
    public function getSupportedCurrenciesWithRates(string $storeId): array
    {
        $config = $this->configRepo->getFirstBy(['store_id' => $storeId]);

        if (!$config) {
            return [];
        }

        $currencies = [];
        $baseCurrency = $config->default_currency;
        $targetCurrencies = $config->display_currencies;

        foreach ($targetCurrencies as $currency) {
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
                    $rateInfo = $this->getEffectiveRate($storeId, $baseCurrency, $currency);
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
     * Update custom rates for a store.
     */
    public function updateCustomRates(string $storeId, array $rates, string $effectiveDate): array
    {
        $config = $this->configRepo->getFirstBy(['store_id' => $storeId]);

        if (!$config) {
            throw new \Exception("Currency configuration not found for store: {$storeId}");
        }

        $baseCurrency = $config->default_currency;
        $results = [];

        foreach ($rates as $targetCurrency => $rate) {
            if ($targetCurrency === $baseCurrency) {
                continue; // Skip base currency
            }

            try {
                // Custom rates are now stored in global table with source='manual'
                $this->globalRateRepo->updateOrCreateRate(
                    $baseCurrency,
                    $targetCurrency,
                    $rate,
                    $effectiveDate,
                    'manual'
                );

                $results[] = [
                    'from_currency' => $baseCurrency,
                    'to_currency' => $targetCurrency,
                    'rate' => $rate,
                    'success' => true,
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'from_currency' => $baseCurrency,
                    'to_currency' => $targetCurrency,
                    'rate' => $rate,
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        // Update last rate update date
        $this->configRepo->updateBy(['store_id' => $storeId], ['last_rate_update' => $effectiveDate]);

        return $results;
    }

    /**
     * Get rate history for a currency pair.
     */
    public function getRateHistory(string $fromCurrency, string $toCurrency, string $startDate, string $endDate, string $storeId): array
    {
        // Get all rates from global table for the date range
        $history = [];

        $globalHistory = $this->globalRateRepo->getHistory(
            $fromCurrency,
            $toCurrency,
            $startDate,
            $endDate,
            []
        );

        foreach ($globalHistory as $rate) {
            $history[] = [
                'date' => $rate->effective_date->format('Y-m-d'),
                'rate' => $rate->rate,
                'source' => $rate->source,
            ];
        }

        // Sort by date
        usort($history, function ($a, $b) {
            return strcmp($a['date'], $b['date']);
        });

        return $history;
    }

    /**
     * Get rate update summary.
     */
    public function getUpdateSummary(int $days): array
    {
        return $this->updateRepo->getRecentSummary($days);
    }

    /**
     * Reset custom rates to use global rates.
     * Since all rates are now in the global table, this method is deprecated.
     * It now just clears manual rates for this store's currency pairs.
     */
    public function resetToGlobal(string $storeId, array $currencies): int
    {
        $config = $this->configRepo->getFirstBy(['store_id' => $storeId]);

        if (!$config) {
            throw new \Exception("Currency configuration not found for store: {$storeId}");
        }

        $baseCurrency = $config->default_currency;
        $deleted = 0;

        foreach ($currencies as $targetCurrency) {
            if ($targetCurrency === $baseCurrency) {
                continue;
            }

            // Delete manual rates from global table
            $deleted += $this->globalRateRepo->deleteBy([
                'source' => 'manual',
                'from_currency' => $baseCurrency,
                'to_currency' => $targetCurrency,
            ]);
        }

        return $deleted;
    }

    /**
     * Get currency metadata (symbol, name, flag).
     */
    private function getCurrencyMetadata(string $currency): array
    {
        $metadata = [
            'USD' => ['symbol' => '$', 'name' => 'US Dollar', 'flag' => '🇺🇸'],
            'EUR' => ['symbol' => '€', 'name' => 'Euro', 'flag' => '🇪🇺'],
            'GBP' => ['symbol' => '£', 'name' => 'British Pound', 'flag' => '🇬🇧'],
            'JPY' => ['symbol' => '¥', 'name' => 'Japanese Yen', 'flag' => '🇯🇵'],
            'COP' => ['symbol' => '$', 'name' => 'Colombian Peso', 'flag' => '🇨🇴'],
            'MXN' => ['symbol' => '$', 'name' => 'Mexican Peso', 'flag' => '🇲🇽'],
            'CAD' => ['symbol' => 'C$', 'name' => 'Canadian Dollar', 'flag' => '🇨🇦'],
            'AUD' => ['symbol' => 'A$', 'name' => 'Australian Dollar', 'flag' => '🇦🇺'],
            'CHF' => ['symbol' => 'Fr', 'name' => 'Swiss Franc', 'flag' => '🇨🇭'],
            'CNY' => ['symbol' => '¥', 'name' => 'Chinese Yuan', 'flag' => '🇨🇳'],
            'INR' => ['symbol' => '₹', 'name' => 'Indian Rupee', 'flag' => '🇮🇳'],
        ];

        return $metadata[$currency] ?? [
            'symbol' => $currency,
            'name' => $currency,
            'flag' => '🏳️'
        ];
    }

    /**
     * Get currency symbol.
     */
    private function getCurrencySymbol(string $currency): string
    {
        return $this->getCurrencyMetadata($currency)['symbol'];
    }

    /**
     * Get currency name.
     */
    private function getCurrencyName(string $currency): string
    {
        return $this->getCurrencyMetadata($currency)['name'];
    }

    /**
     * Get currency flag.
     */
    private function getCurrencyFlag(string $currency): string
    {
        return $this->getCurrencyMetadata($currency)['flag'];
    }

    /**
     * Calculate price in target currency.
     */
    public function calculatePrice(float $amount, string $fromCurrency, string $toCurrency, string $storeId, ?string $date = null): array
    {
        if ($fromCurrency === $toCurrency) {
            return [
                'amount' => $amount,
                'rate' => 1.0,
                'source' => 'same_currency',
            ];
        }

        $rateInfo = $this->getEffectiveRate($storeId, $fromCurrency, $toCurrency, $date);

        return [
            'amount' => round($amount * $rateInfo['rate'], 2),
            'rate' => $rateInfo['rate'],
            'source' => $rateInfo['source'],
            'effective_date' => $rateInfo['effective_date'],
        ];
    }

    /**
     * Get currency performance data for dashboard.
     */
    public function getCurrencyPerformance(string $storeId, int $days = 30): array
    {
        $config = $this->configRepo->getFirstBy(['store_id' => $storeId]);

        if (!$config) {
            return [];
        }

        $baseCurrency = $config->default_currency;
        $targetCurrencies = $config->display_currencies;
        $performance = [];

        foreach ($targetCurrencies as $currency) {
            if ($currency === $baseCurrency) {
                continue;
            }

            // Obtener tasas históricas
            $endDate = now()->format('Y-m-d');
            $startDate = now()->subDays($days)->format('Y-m-d');

            $history = $this->getRateHistory($baseCurrency, $currency, $startDate, $endDate, $storeId);

            if (count($history) >= 2) {
                $firstRate = $history[0]['rate'];
                $lastRate = end($history)['rate'];
                $change = (($lastRate - $firstRate) / $firstRate) * 100;

                $performance[] = [
                    'currency' => $currency,
                    'current_rate' => $lastRate,
                    'change_percent' => round($change, 2),
                    'trend' => $change >= 0 ? 'up' : 'down',
                ];
            }
        }

        return $performance;
    }
}
