<?php

namespace App\Services\Currency\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiCurrencyProvider extends BaseCurrencyProvider
{
    public function getName(): string
    {
        return 'API Provider';
    }

    public function getType(): string
    {
        return 'api';
    }

    /**
     * Fetch rates from REST API.
     * Makes one API call per supported currency as base, extracting rates directly from 'rates' field.
     */
    public function fetchRates(array $currencyPairs): array
    {
        try {
            $endpointTemplate = $this->config['rates_endpoint'] ?? '/latest.json';
            $supportedCurrencies = $this->config['currencies_supported'] ?? $this->config['supported_currencies'] ?? ['USD'];

            $allRates = [];

            // Make one API call per supported currency as base
            foreach ($supportedCurrencies as $fromCurrency) {
                $fromCurrency = strtoupper($fromCurrency);

                // Replace {base_currency} placeholder in endpoint
                $endpoint = str_replace('{base_currency}', $fromCurrency, $endpointTemplate);

                // Replace {api_key} placeholder if present
                $apiKey = $this->credentials['api_key'] ?? '';
                $endpoint = str_replace('{api_key}', $apiKey, $endpoint);

                $url = rtrim($this->baseUrl, '/') . $endpoint;

                // Build query parameters (skip if api_key already in URL)
                $params = [];
                if ($this->config['auth_type'] === 'api_key' &&
                    ($this->config['api_key_location'] ?? 'query') === 'query' &&
                    !str_contains($endpoint, $apiKey)) {
                    $params[$this->config['api_key_name'] ?? 'api_key'] = $apiKey;
                }

                $response = $this->httpClient()->get($url, $params);

                if (!$response->successful()) {
                    $this->logError('API request failed', [
                        'url' => $url,
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                    continue;
                }

                $data = $response->json();
                $rates = $data['rates'] ?? $data['conversion_rates'] ?? [];


                // Initialize rates array for this base currency
                $allRates[$fromCurrency] = [];

                // Extract rates directly from the 'rates' field
                foreach ($supportedCurrencies as $toCurrency) {
                    $toCurrency = strtoupper($toCurrency);

                    // Skip when from == to
                    if ($fromCurrency === $toCurrency) {
                        continue;
                    }

                    // Get rate directly from API response (fromCurrency is base)
                    if (isset($rates[$toCurrency])) {
                        $allRates[$fromCurrency][$toCurrency] = (float) $rates[$toCurrency];
                    }
                }
            }

            return $allRates;

        } catch (\Exception $e) {
            $this->logError('Exception fetching rates', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Get sample data from response for logging (first 3 rates only).
     */
    protected function getSampleData(array $data): array
    {
        $rates = $data['rates'] ?? $data['conversion_rates'] ?? [];
        return array_slice($rates, 0, 3, true);
    }

    /**
     * Parse rates from API response for a specific batch of pairs.
     */
    protected function parseRatesForData(array $data, array $pairs, string $baseCurrency): array
    {
        $rates = [];
        $responseFormat = $this->config['response_format'] ?? 'openexchange';

        foreach ($pairs as $pair) {
            $from = strtoupper($pair['from']);
            $to = strtoupper($pair['to']);

            $rate = match ($responseFormat) {
                'openexchange' => $this->parseOpenExchangeFormat($data, $from, $to, $baseCurrency),
                'exchangerate-api' => $this->parseExchangeRateApiFormat($data, $from, $to),
                'fixer' => $this->parseFixerFormat($data, $from, $to),
                default => $this->parseGenericFormat($data, $from, $to),
            };

            if ($rate !== null) {
                $rates[$from][$to] = $rate;
            }
        }

        return $rates;
    }

    /**
     * Parse OpenExchangeRates format.
     */
    protected function parseOpenExchangeFormat(array $data, string $from, string $to, string $baseCurrency): ?float
    {
        $rates = $data['rates'] ?? [];

        if ($from === $baseCurrency) {
            return $rates[$to] ?? null;
        }

        // Convert via base currency
        $fromRate = $rates[$from] ?? null;
        $toRate = $rates[$to] ?? null;

        if ($fromRate && $toRate) {
            return $toRate / $fromRate;
        }

        return null;
    }

    /**
     * Parse ExchangeRate-API format.
     */
    protected function parseExchangeRateApiFormat(array $data, string $from, string $to): ?float
    {
        $rates = $data['rates'] ?? $data['conversion_rates'] ?? [];
        return $rates[$to] ?? null;
    }

    /**
     * Parse Fixer format.
     */
    protected function parseFixerFormat(array $data, string $from, string $to): ?float
    {
        if (isset($data['rates'][$to])) {
            return $data['rates'][$to];
        }

        // Fixer uses specific endpoints per pair sometimes
        if (isset($data['rate'])) {
            return (float) $data['rate'];
        }

        return null;
    }

    /**
     * Parse generic format (tries common patterns).
     */
    protected function parseGenericFormat(array $data, string $from, string $to): ?float
    {
        // Try common patterns
        $paths = [
            "rates.{$to}",
            "data.{$to}",
            "{$from}_{to}",
            "rates.{$from}.{$to}",
            "result",
        ];

        foreach ($paths as $path) {
            $value = $this->getNestedValue($data, explode('.', $path));
            if ($value !== null) {
                return (float) $value;
            }
        }

        return null;
    }

    /**
     * Get nested array value by path.
     */
    protected function getNestedValue(array $array, array $path): mixed
    {
        $current = $array;

        foreach ($path as $key) {
            if (!isset($current[$key])) {
                return null;
            }
            $current = $current[$key];
        }

        return $current;
    }

    /**
     * Test API connection.
     */
    public function testConnection(): array
    {
        try {
            $endpoint = $this->config['test_endpoint'] ?? $this->config['rates_endpoint'] ?? '/latest.json';
            $url = rtrim($this->baseUrl, '/') . $endpoint;

            $params = [];
            if ($this->config['auth_type'] === 'api_key' && ($this->config['api_key_location'] ?? 'query') === 'query') {
                $params[$this->config['api_key_name'] ?? 'api_key'] = $this->credentials['api_key'] ?? '';
            }

            $response = $this->httpClient()->get($url, $params);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'message' => "HTTP {$response->status()}: " . $response->body(),
                    'rates_found' => 0,
                ];
            }

            $data = $response->json();
            $rateCount = count($data['rates'] ?? $data['conversion_rates'] ?? []);

            return [
                'success' => true,
                'message' => "Connection successful. Found {$rateCount} currencies.",
                'rates_found' => $rateCount,
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
