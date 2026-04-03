<?php

namespace App\Services\Currency\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Banco Central de Cuba (BCC) Provider
 * Official exchange rates from Cuba's Central Bank
 * API: https://api.bc.gob.cu/v1/tasas-de-cambio/activas
 */
class BccCubaProvider extends BaseCurrencyProvider
{
    public function getName(): string
    {
        return 'Banco Central de Cuba';
    }

    public function getType(): string
    {
        return 'api';
    }

    /**
     * Fetch rates from BCC API.
     * CLA (Tarjeta Clasica) is treated as USD.
     */
    public function fetchRates(array $currencyPairs): array
    {
        try {
            $pairs = $this->normalizePairs($currencyPairs);
            $endpoint = $this->config['rates_endpoint'] ?? '/v1/tasas-de-cambio/activas';
            $url = rtrim($this->baseUrl, '/') . $endpoint;

            $response = $this->httpClient()->get($url);

            if (!$response->successful()) {
                $this->logError('BCC API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return [];
            }

            $data = $response->json();
            return $this->parseBccRates($data, $pairs);

        } catch (\Exception $e) {
            $this->logError('Exception fetching BCC rates', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Parse BCC API response format.
     * Expected format based on API documentation:
     * {
     *   "data": [
     *     {"moneda": "USD", "compra": 120.00, "venta": 130.00, "fecha": "2026-04-03"},
     *     {"moneda": "EUR", "compra": 130.00, "venta": 140.00, "fecha": "2026-04-03"}
     *   ]
     * }
     */
    protected function parseBccRates(array $data, array $pairs): array
    {
        $rates = [];
        
        // BCC uses "venta" (sell) rate for conversions
        $bccRates = [];
        $rateList = $data['data'] ?? $data['tasas'] ?? $data['rates'] ?? [];
        
        foreach ($rateList as $rateData) {
            $currency = strtoupper($rateData['moneda'] ?? $rateData['currency'] ?? '');
            $value = $rateData['venta'] ?? $rateData['sell'] ?? $rateData['rate'] ?? null;
            
            if ($currency && $value) {
                $bccRates[$currency] = (float) $value;
            }
        }

        // Process each pair
        foreach ($pairs as $pair) {
            $from = strtoupper($pair['from']);
            $to = strtoupper($pair['to']);

            // CLA (Tarjeta Clasica) equals USD
            if ($from === 'CLA') $from = 'USD';
            if ($to === 'CLA') $to = 'USD';

            $rate = $this->calculateRate($from, $to, $bccRates);
            
            if ($rate !== null) {
                $rates[$pair['from']][$pair['to']] = $rate;
            }
        }

        return $rates;
    }

    /**
     * Calculate rate between two currencies.
     * BCC rates are relative to CUP (base currency).
     */
    protected function calculateRate(string $from, string $to, array $bccRates): ?float
    {
        $baseCurrency = strtoupper($this->config['base_currency'] ?? 'CUP');

        // If both are the same
        if ($from === $to) {
            return 1.0;
        }

        // Direct rate lookup (if available)
        $directKey = "{$from}_{$to}";
        if (isset($bccRates[$directKey])) {
            return $bccRates[$directKey];
        }

        // Calculate via base currency (CUP)
        $fromRate = $bccRates[$from] ?? null;
        $toRate = $bccRates[$to] ?? null;

        if ($from === $baseCurrency && $toRate) {
            // CUP to X: direct rate
            return $toRate;
        }

        if ($to === $baseCurrency && $fromRate) {
            // X to CUP: inverse rate
            return 1 / $fromRate;
        }

        if ($fromRate && $toRate) {
            // X to Y via CUP: (CUP/Y) / (CUP/X) = X/Y
            return $toRate / $fromRate;
        }

        return null;
    }

    /**
     * Test BCC API connection.
     */
    public function testConnection(): array
    {
        try {
            $endpoint = $this->config['test_endpoint'] ?? '/v1/tasas-de-cambio/activas';
            $url = rtrim($this->baseUrl, '/') . $endpoint;
            
            $response = $this->httpClient()->get($url);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => "HTTP {$response->status()}: " . $response->body(),
                    'rates_found' => 0,
                ];
            }

            $data = $response->json();
            $rateList = $data['data'] ?? $data['tasas'] ?? $data['rates'] ?? [];
            $rateCount = count($rateList);

            if ($rateCount === 0) {
                return [
                    'success' => false,
                    'message' => 'API returned empty rate list',
                    'rates_found' => 0,
                ];
            }

            // Check for CUP rates
            $currencies = [];
            foreach ($rateList as $rate) {
                $currency = $rate['moneda'] ?? $rate['currency'] ?? '';
                if ($currency) {
                    $currencies[] = $currency;
                }
            }

            return [
                'success' => true,
                'message' => "Connected. Found {$rateCount} currencies: " . implode(', ', $currencies),
                'rates_found' => $rateCount,
                'currencies' => $currencies,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'rates_found' => 0,
            ];
        }
    }
}
