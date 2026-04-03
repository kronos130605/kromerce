<?php

namespace App\Services\Currency\Providers;

use App\Services\Currency\Providers\Contracts\CurrencyProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class BaseCurrencyProvider implements CurrencyProviderInterface
{
    protected string $baseUrl;
    protected array $config;
    protected ?array $credentials;
    protected ?string $lastError = null;

    public function __construct(string $baseUrl, array $config = [], ?array $credentials = null)
    {
        $this->baseUrl = $baseUrl;
        $this->config = $config;
        $this->credentials = $credentials;
    }

    /**
     * Build HTTP client with auth headers.
     */
    protected function httpClient()
    {
        $client = Http::timeout($this->config['timeout'] ?? 30)
            ->withHeaders($this->config['headers'] ?? []);

        // Add authentication
        if ($this->credentials) {
            match ($this->config['auth_type'] ?? 'none') {
                'api_key' => $this->addApiKeyAuth($client),
                'basic' => $client->withBasicAuth($this->credentials['username'] ?? '', $this->credentials['password'] ?? ''),
                'bearer' => $client->withToken($this->credentials['token'] ?? ''),
                default => null,
            };
        }

        return $client;
    }

    /**
     * Add API key to request (query param or header based on config).
     */
    protected function addApiKeyAuth($client)
    {
        $apiKey = $this->credentials['api_key'] ?? null;
        
        if (!$apiKey) {
            return $client;
        }

        $keyLocation = $this->config['api_key_location'] ?? 'query';
        $keyName = $this->config['api_key_name'] ?? 'api_key';

        if ($keyLocation === 'header') {
            return $client->withHeader($keyName, $apiKey);
        }

        // Default: query parameter
        return $client->withQueryParameters([$keyName => $apiKey]);
    }

    /**
     * Log provider error.
     */
    protected function logError(string $message, array $context = []): void
    {
        $this->lastError = $message;
        
        Log::error("Currency Provider [{$this->getName()}]: {$message}", array_merge($context, [
            'provider' => $this->getName(),
            'base_url' => $this->baseUrl,
        ]));
    }

    /**
     * Get last error.
     */
    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    /**
     * Check if currency is supported.
     */
    public function supportsCurrency(string $currency): bool
    {
        $supported = $this->config['currencies_supported'] ?? [];
        
        if (empty($supported)) {
            return true; // Assume all supported if not specified
        }

        return in_array(strtoupper($currency), $supported);
    }

    /**
     * Normalize currency pairs for fetching.
     * Input: [['from' => 'USD', 'to' => 'VES'], ['from' => 'USD', 'to' => 'COP']]
     * Or: ['USD' => ['VES', 'COP']]
     */
    protected function normalizePairs(array $pairs): array
    {
        $normalized = [];

        // Check if associative array (USD => [VES, COP])
        if (array_keys($pairs) !== range(0, count($pairs) - 1)) {
            foreach ($pairs as $from => $tos) {
                foreach ((array) $tos as $to) {
                    $normalized[] = ['from' => $from, 'to' => $to];
                }
            }
            return $normalized;
        }

        // Already in pair format
        return $pairs;
    }
}
