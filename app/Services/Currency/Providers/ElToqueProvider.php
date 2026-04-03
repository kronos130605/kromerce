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

            $rates = [];
            $pairs = $this->normalizePairs($currencyPairs);

            foreach ($pairs as $pair) {
                $from = strtoupper($pair['from']);
                $to = strtoupper($pair['to']);

                // CLA (Tarjeta Clasica) equals USD
                if ($from === 'CLA') $from = 'USD';
                if ($to === 'CLA') $to = 'USD';

                $rate = $this->calculateRate($from, $to, $scrapedRates);
                
                if ($rate !== null) {
                    $rates[$pair['from']][$pair['to']] = $rate;
                }
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
     * ElToque structure typically has rate cards or tables.
     */
    protected function extractRates(Crawler $crawler): array
    {
        $rates = [];
        $selectors = $this->config['selectors'] ?? [];

        // Try to extract each currency rate
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

        // If no rates found with specific selectors, try generic patterns
        if (empty($rates)) {
            $rates = $this->extractGenericRates($crawler);
        }

        // CUP is always 1 (base currency)
        $rates['CUP'] = 1.0;

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
     * Extract rates using generic patterns when specific selectors fail.
     */
    protected function extractGenericRates(Crawler $crawler): array
    {
        $rates = [];
        
        try {
            // Look for patterns like "USD: 350 CUP" or similar
            $currencies = ['USD', 'EUR', 'MLC'];
            
            foreach ($currencies as $currency) {
                // Try multiple patterns
                $patterns = [
                    // Pattern: currency code followed by number
                    "//text()[contains(., '{$currency}') and contains(., 'CUP')]",
                    "//div[contains(@class, '{$currency}')]",
                    "//span[contains(text(), '{$currency}')]",
                ];
                
                foreach ($patterns as $pattern) {
                    try {
                        $elements = $crawler->filterXPath($pattern);
                        if ($elements->count() > 0) {
                            $text = $elements->first()->text();
                            $value = $this->parseRateValue($text);
                            if ($value !== null) {
                                $rates[$currency] = $value;
                                break;
                            }
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }

        } catch (\Exception $e) {
            Log::warning('ElToque generic extraction failed', ['error' => $e->getMessage()]);
        }

        return $rates;
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
     * Calculate rate between two currencies.
     * ElToque rates are relative to CUP.
     */
    protected function calculateRate(string $from, string $to, array $scrapedRates): ?float
    {
        $baseCurrency = strtoupper($this->config['base_currency'] ?? 'CUP');

        if ($from === $to) {
            return 1.0;
        }

        // Get rates
        $fromRate = $scrapedRates[$from] ?? null;
        $toRate = $scrapedRates[$to] ?? null;

        if ($from === $baseCurrency && $toRate) {
            // CUP to X
            return $toRate;
        }

        if ($to === $baseCurrency && $fromRate) {
            // X to CUP
            return 1 / $fromRate;
        }

        if ($fromRate && $toRate) {
            // X to Y via CUP
            return $toRate / $fromRate;
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
