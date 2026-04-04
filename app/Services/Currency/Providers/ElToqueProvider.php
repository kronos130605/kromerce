<?php

namespace App\Services\Currency\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

/**
 * ElToque Provider
 * Informal market exchange rates for Cuba via web scraping
 * URL: https://eltoque.com/tasas-de-cambio-cuba/mercado-informal
 */
class ElToqueProvider extends BaseCurrencyProvider
{
    public function getName(): string
    {
        return 'ElToque - Mercado Informal';
    }

    public function getType(): string
    {
        return 'web';
    }

    /**
     * Fetch rates by scraping ElToque website.
     * CLA (Tarjeta Clasica) is treated as USD.
     */
    public function fetchRates(array $currencyPairs): array
    {
        try {
            $html = $this->fetchPage();

            if (empty($html)) {
                $this->logError('Empty page content from ElToque');
                return [];
            }

            $crawler = new Crawler($html);
            $scrapedRates = $this->extractRates($crawler);

            if (empty($scrapedRates)) {
                $this->logError('Could not extract rates from ElToque page');
                return [];
            }

            // ElToque rates are always relative to CUP
            // Return rates as X->CUP (e.g., USD->CUP = 518.0)
            $rates = [];
            $baseCurrency = 'CUP';

            foreach ($scrapedRates as $currency => $rate) {
                if ($currency === $baseCurrency) {
                    continue; // Skip CUP->CUP (would be 1.0)
                }

                // Rate is: 1 foreign currency = X CUP
                $rates[$currency][$baseCurrency] = $rate;
            }

            return $rates;

        } catch (\Exception $e) {
            $this->logError('Exception scraping ElToque rates', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Fetch the web page content.
     */
    protected function fetchPage(): ?string
    {
        try {
            $headers = array_merge(
                $this->config['headers'] ?? [],
                [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language' => 'es-ES,es;q=0.9,en;q=0.8',
                    'Accept-Encoding' => 'gzip, deflate, br',
                    'Connection' => 'keep-alive',
                ]
            );

            $response = Http::withHeaders($headers)
                ->timeout($this->config['timeout'] ?? 30)
                ->get($this->baseUrl);

            if (!$response->successful()) {
                $this->logError('Failed to fetch ElToque page', [
                    'status' => $response->status(),
                ]);
                return null;
            }

            return $response->body();

        } catch (\Exception $e) {
            $this->logError('Failed to fetch ElToque page', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Extract rates from the HTML.
     * ElToque structure: table rows with currency in cell-title-v2-X and rate in font-extrabold spans
     */
    protected function extractRates(Crawler $crawler): array
    {
        $rates = [];
        $selectors = $this->config['selectors'] ?? [];

        // If selectors are configured, try them first
        if (!empty($selectors)) {
            $currencies = ['USD', 'EUR', 'MLC'];

            foreach ($currencies as $currency) {
                $selector = $selectors[strtolower($currency)] ?? null;

                if ($selector) {
                    $value = $this->extractWithSelector($crawler, $selector);

                    if ($value !== null) {
                        $rates[$currency] = $value;
                    }
                }
            }
        }

        // If no rates found with specific selectors, use table-based extraction
        if (empty($rates)) {
            $rates = $this->extractFromTable($crawler);
        }

        // CUP is always 1 (base currency)
        $rates['CUP'] = 1.0;

        Log::debug('ElToque extractRates completed', ['rates' => $rates]);

        return $rates;
    }

    /**
     * Extract rates from the ElToque table structure.
     * Parses the table row by row, looking for currency codes and rate values.
     */
    protected function extractFromTable(Crawler $crawler): array
    {
        $rates = [];

        try {
            // Try to find the rates table - it has specific structure
            // First, try to find by the specific table class pattern
            $table = $crawler->filter('table.border-collapse');

            if ($table->count() === 0) {
                // Fallback: find any table with the cell-title spans
                $table = $crawler->filter('table:has(span[id^="cell-title-v2-"])');
            }

            if ($table->count() === 0) {
                Log::debug('ElToque no rates table found');
                return $rates;
            }

            // Get all rows from the table body
            $rows = $table->first()->filter('tbody tr, tr');

            Log::debug('ElToque table extraction', [
                'table_found' => true,
                'rows_found' => $rows->count(),
            ]);

            $rows->each(function (Crawler $row, $i) use (&$rates) {
                // Find currency span in this row (1st td)
                $currencySpan = $row->filter('span[id^="cell-title-v2-"]');

                if ($currencySpan->count() === 0) {
                    return; // Skip rows without currency
                }

                $currencyText = $currencySpan->text();

                // Parse currency code: "1 USD", "1 EUR", etc.
                // Handle non-breaking space (&nbsp;)
                if (!preg_match('/1\s*([A-Z]{3})/u', $currencyText, $currencyMatch)) {
                    Log::debug('ElToque currency regex failed', ['text' => $currencyText, 'row' => $i]);
                    return;
                }

                $currency = $currencyMatch[1];

                // Find rate value in this row (3rd td contains div with the rate span)
                $rateSpan = $row->filter('td:nth-child(3) span.font-extrabold.text-lg');

                if ($rateSpan->count() === 0) {
                    // Try alternative: any font-extrabold in the row
                    $rateSpan = $row->filter('span.font-extrabold.text-lg');
                }

                if ($rateSpan->count() === 0) {
                    Log::debug('ElToque no rate span found', ['row' => $i, 'currency' => $currency]);
                    return;
                }

                $rateText = $rateSpan->first()->text();

                // Parse rate value: "518.00 CUP" (handle &nbsp;)
                if (!preg_match('/([\d.,]+)\s*CUP/u', $rateText, $rateMatch)) {
                    Log::debug('ElToque rate regex failed', ['text' => $rateText, 'currency' => $currency]);
                    return;
                }

                $value = str_replace(',', '', $rateMatch[1]);
                $floatValue = (float) $value;

                // Validate reasonable rate for Cuban informal market
                if ($floatValue > 10 && $floatValue < 10000) {
                    $rates[$currency] = $floatValue;
                }
            });

        } catch (\Exception $e) {
            Log::warning('ElToque table extraction failed', ['error' => $e->getMessage()]);
        }

        Log::debug('ElToque extracted rates from table', ['rates' => $rates]);

        return $rates;
    }

    /**
     * Extract value using CSS selector.
     */
    protected function extractWithSelector(Crawler $crawler, string $selector): ?float
    {
        try {
            $element = $crawler->filter($selector);

            if ($element->count() === 0) {
                return null;
            }

            $text = $element->text();
            return $this->parseRateValue($text);

        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Parse rate value from text.
     */
    protected function parseRateValue(string $text): ?float
    {
        $text = trim($text);

        // Clean common prefixes/suffixes
        $text = str_replace(['$', '€', '£', 'CUP', 'MLC', ','], ['', '', '', '', '', ''], $text);

        // Extract numeric value
        // Patterns: "350", "350.50", "350,50", "1:350", "350 CUP"
        if (preg_match('/(\d+[.,]?\d*)/', $text, $matches)) {
            $value = str_replace(',', '.', $matches[1]);
            $float = (float) $value;

            // Validate reasonable rate (10 to 10000 for informal market)
            if ($float > 10 && $float < 10000) {
                return $float;
            }
        }

        return null;
    }

    /**
     * Test ElToque connection.
     */
    public function testConnection(): array
    {
        try {
            $html = $this->fetchPage();

            if (empty($html)) {
                return [
                    'success' => false,
                    'message' => 'Could not fetch ElToque page',
                    'rates_found' => 0,
                ];
            }

            $crawler = new Crawler($html);
            $rates = $this->extractRates($crawler);
            $rateCount = count($rates) - 1; // Exclude CUP

            if ($rateCount === 0) {
                return [
                    'success' => false,
                    'message' => 'Could not extract rates from page. Selectors may need updating.',
                    'rates_found' => 0,
                ];
            }

            return [
                'success' => true,
                'message' => "Connected. Found rates for: " . implode(', ', array_keys(array_diff_key($rates, ['CUP' => 1]))),
                'rates_found' => $rateCount,
                'currencies' => array_keys($rates),
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
