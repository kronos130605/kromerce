<?php

namespace App\Services\Currency\Providers\Contracts;

interface CurrencyProviderInterface
{
    /**
     * Fetch rates for given currency pairs.
     * Returns array like: ['USD' => ['VES' => 36.45, 'COP' => 3950]]
     */
    public function fetchRates(array $currencyPairs): array;

    /**
     * Test if the provider connection works.
     * Returns ['success' => bool, 'message' => string, 'rates_found' => int]
     */
    public function testConnection(): array;

    /**
     * Get provider name.
     */
    public function getName(): string;

    /**
     * Get provider type (api or web).
     */
    public function getType(): string;

    /**
     * Check if provider supports a specific currency.
     */
    public function supportsCurrency(string $currency): bool;

    /**
     * Get last error message if any.
     */
    public function getLastError(): ?string;
}
