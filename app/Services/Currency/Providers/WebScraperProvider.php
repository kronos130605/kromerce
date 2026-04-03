<?php

namespace App\Services\Currency\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

class WebScraperProvider extends BaseCurrencyProvider
{
    public function getName(): string
    {
        return 'Web Scraper';
    }

    public function getType(): string
    {
        return 'web';
    }

    /**
     * Fetch rates by scraping web page.
     */
    public function fetchRates(array $currencyPairs): array
    {
        try {
            $html = $this->fetchPage();
            
            if (empty($html)) {
                $this->logError('Empty page content');
                return [];
            }

            $crawler = new Crawler($html);
            $rates = [];

            foreach ($currencyPairs as $pair) {
                $from = strtoupper($pair['from']);
                $to = strtoupper($pair['to']);

                $rate = $this->extractRate($crawler, $from, $to);
                
                if ($rate !== null) {
                    $rates[$from][$to] = $rate;
                }
            }

            return $rates;

        } catch (\Exception $e) {
            $this->logError('Exception scraping rates', [
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
                    'Accept-Language' => 'en-US,en;q=0.5',
                ]
            );

            $response = Http::withHeaders($headers)
                ->timeout($this->config['timeout'] ?? 30)
                ->get($this->baseUrl);

            if (!$response->successful()) {
                $this->logError('Failed to fetch page', [
                    'status' => $response->status(),
                ]);
                return null;
            }

            return $response->body();

        } catch (\Exception $e) {
            $this->logError('Failed to fetch page', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Extract rate from crawler for specific currency pair.
     */
    protected function extractRate(Crawler $crawler, string $from, string $to): ?float
    {
        $selectors = $this->config['selectors'] ?? [];

        // Try currency-specific selector first
        $specificSelector = $selectors["{$from}_{$to}"] ?? $selectors[strtolower("{$from}_{$to}")] ?? null;
        
        if ($specificSelector) {
            $rate = $this->extractWithSelector($crawler, $specificSelector);
            if ($rate !== null) {
                return $rate;
            }
        }

        // Try generic rate selector
        $genericSelector = $selectors['rate'] ?? null;
        
        if ($genericSelector) {
            $rate = $this->extractWithSelector($crawler, $genericSelector);
            if ($rate !== null) {
                return $rate;
            }
        }

        // Try table-based extraction
        return $this->extractFromTable($crawler, $from, $to);
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
     * Extract rate from HTML table.
     */
    protected function extractFromTable(Crawler $crawler, string $from, string $to): ?float
    {
        try {
            // Look for tables containing currency data
            $tables = $crawler->filter('table');
            
            foreach ($tables as $table) {
                $tableCrawler = new Crawler($table);
                $rows = $tableCrawler->filter('tr');
                
                foreach ($rows as $row) {
                    $rowCrawler = new Crawler($row);
                    $cells = $rowCrawler->filter('td, th');
                    
                    if ($cells->count() < 2) {
                        continue;
                    }
                    
                    $cellTexts = [];
                    foreach ($cells as $cell) {
                        $cellTexts[] = trim((new Crawler($cell))->text());
                    }
                    
                    // Check if this row contains our currency pair
                    $rowText = implode(' ', $cellTexts);
                    
                    if (stripos($rowText, $from) !== false && stripos($rowText, $to) !== false) {
                        // Try to find a numeric value in the cells
                        foreach ($cellTexts as $text) {
                            $rate = $this->parseRateValue($text);
                            if ($rate !== null) {
                                return $rate;
                            }
                        }
                    }
                }
            }

            return null;

        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Parse rate value from text.
     */
    protected function parseRateValue(string $text): ?float
    {
        // Clean the text
        $text = trim($text);
        
        // Remove currency symbols and normalize
        $text = str_replace(['$', '€', '£', '¥', 'Bs.', 'Bs', 'VES', 'USD', ','], ['', '', '', '', '', '', '', '', ''], $text);
        
        // Extract numeric value (handle formats like "36.45", "36,45", "1:36.45")
        if (preg_match('/[\d:.]+/', $text, $matches)) {
            $value = str_replace([':', ' '], ['.', ''], $matches[0]);
            $value = str_replace(',', '.', $value);
            
            $float = (float) $value;
            
            // Validate reasonable rate (0.0001 to 1000000)
            if ($float > 0 && $float < 1000000) {
                return $float;
            }
        }

        return null;
    }

    /**
     * Test web scraping connection.
     */
    public function testConnection(): array
    {
        try {
            $html = $this->fetchPage();
            
            if (empty($html)) {
                return [
                    'success' => false,
                    'message' => 'Could not fetch page content',
                    'rates_found' => 0,
                ];
            }

            $crawler = new Crawler($html);
            
            // Try to extract any rate to verify selectors work
            $testSelector = $this->config['selectors']['rate'] ?? null;
            $ratesFound = 0;
            $message = 'Page fetched successfully';
            
            if ($testSelector) {
                $elements = $crawler->filter($testSelector);
                $ratesFound = $elements->count();
                
                if ($ratesFound > 0) {
                    $message = "Found {$ratesFound} elements matching selector '{$testSelector}'";
                } else {
                    $message = "No elements found for selector '{$testSelector}'. Check your CSS selectors.";
                }
            }

            return [
                'success' => true,
                'message' => $message,
                'rates_found' => $ratesFound,
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
