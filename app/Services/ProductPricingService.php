<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Repositories\Store\StoreCurrencyConfigRepository;
use App\Repositories\Product\ProductRepository;
use Illuminate\Support\Collection;

class ProductPricingService
{
    private StoreCurrencyConfigRepository $configRepo;
    private ProductRepository $productRepo;
    private CurrencyRateService $currencyService;

    public function __construct(
        StoreCurrencyConfigRepository $configRepo,
        ProductRepository $productRepo,
        CurrencyRateService $currencyService
    ) {
        $this->configRepo = $configRepo;
        $this->productRepo = $productRepo;
        $this->currencyService = $currencyService;
    }
    /**
     * Calculate all prices for a product in supported currencies.
     */
    public function calculateProductPrices(Product $product, ?string $targetCurrency = null): array
    {
        $currencyConfig = $this->configRepo->getFirstBy([
            'store_id' => $product->store_id
        ]);

        if (!$currencyConfig) {
            return [];
        }

        $supportedCurrencies = $this->currencyService->getSupportedCurrenciesWithRates((string) $product->store_id);
        $calculatedPrices = [];

        foreach ($supportedCurrencies as $currency => $currencyInfo) {
            if ($targetCurrency && $currency !== $targetCurrency) {
                continue;
            }

            $calculatedPrices[$currency] = [
                'currency' => $currency,
                'symbol' => $currencyInfo['symbol'],
                'flag' => $currencyInfo['flag'],
                'name' => $currencyInfo['name'],
                'rate' => $currencyInfo['rate'],
                'source' => $currencyInfo['source'],
            ];

            // Calculate base price
            $calculatedPrices[$currency]['price'] = $this->convertAmount(
                $product->base_price,
                $product->base_currency,
                $currency,
                $product->store_id
            );

            // Calculate sale price if applicable
            if ($product->is_on_sale && $product->base_sale_price) {
                $calculatedPrices[$currency]['sale_price'] = $this->convertAmount(
                    $product->base_sale_price,
                    $product->base_currency,
                    $currency,
                    $product->store_id
                );
            }

            // Calculate cost price if tracking is enabled
            if ($product->track_cost && $product->cost_price) {
                $calculatedPrices[$currency]['cost_price'] = $this->convertAmount(
                    $product->cost_price,
                    $product->base_currency,
                    $currency,
                    $product->store_id
                );

                // Calculate margin
                if ($calculatedPrices[$currency]['price'] && $calculatedPrices[$currency]['cost_price']) {
                    $calculatedPrices[$currency]['margin'] = $this->calculateMargin(
                        $calculatedPrices[$currency]['price'],
                        $calculatedPrices[$currency]['cost_price']
                    );
                }
            }

            // Calculate historical cost if available
            if ($product->historical_cost_amount && $product->historical_cost_currency) {
                $calculatedPrices[$currency]['historical_cost'] = $this->convertHistoricalCost(
                    $product,
                    $currency
                );
            }
        }

        return $targetCurrency ? $calculatedPrices[$targetCurrency] ?? [] : $calculatedPrices;
    }

    /**
     * Calculate prices for a product variant.
     */
    public function calculateVariantPrices(ProductVariant $variant, ?string $targetCurrency = null): array
    {
        $product = $variant->product;
        $currencyConfig = $this->configRepo->getFirstBy([
            'store_id' => $product->store_id
        ]);

        if (!$currencyConfig) {
            return [];
        }

        $supportedCurrencies = $this->currencyService->getSupportedCurrenciesWithRates((string) $product->store_id);
        $calculatedPrices = [];

        foreach ($supportedCurrencies as $currency => $currencyInfo) {
            if ($targetCurrency && $currency !== $targetCurrency) {
                continue;
            }

            $calculatedPrices[$currency] = [
                'currency' => $currency,
                'symbol' => $currencyInfo['symbol'],
                'flag' => $currencyInfo['flag'],
                'name' => $currencyInfo['name'],
                'rate' => $currencyInfo['rate'],
                'source' => $currencyInfo['source'],
            ];

            // Use variant price if available, otherwise use product price
            $basePrice = $variant->base_price ?: $product->base_price;
            $baseCurrency = $product->base_currency;

            $calculatedPrices[$currency]['price'] = $this->convertAmount(
                $basePrice,
                $baseCurrency,
                $currency,
                $product->store_id
            );

            // Calculate sale price
            $salePrice = $variant->base_sale_price ?: ($product->is_on_sale ? $product->base_sale_price : null);
            if ($salePrice) {
                $calculatedPrices[$currency]['sale_price'] = $this->convertAmount(
                    $salePrice,
                    $baseCurrency,
                    $currency,
                    $product->store_id
                );
            }

            // Calculate cost price
            $costPrice = $variant->cost_price ?: ($product->track_cost ? $product->cost_price : null);
            if ($costPrice) {
                $calculatedPrices[$currency]['cost_price'] = $this->convertAmount(
                    $costPrice,
                    $baseCurrency,
                    $currency,
                    $product->store_id
                );

                // Calculate margin
                if ($calculatedPrices[$currency]['price'] && $calculatedPrices[$currency]['cost_price']) {
                    $calculatedPrices[$currency]['margin'] = $this->calculateMargin(
                        $calculatedPrices[$currency]['price'],
                        $calculatedPrices[$currency]['cost_price']
                    );
                }
            }
        }

        return $targetCurrency ? $calculatedPrices[$targetCurrency] ?? [] : $calculatedPrices;
    }

    /**
     * Convert amount between currencies using historical rates if needed.
     */
    private function convertAmount(float $amount, string $fromCurrency, string $toCurrency, string $storeId): float
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        try {
            $conversion = $this->currencyService->calculatePrice($amount, $fromCurrency, $toCurrency, $storeId);

            return $conversion['amount'];
        } catch (\Exception $e) {
            // Fallback to 1:1 if rate not found
            return $amount;
        }
    }

    /**
     * Convert historical cost to target currency.
     */
    private function convertHistoricalCost(Product $product, string $targetCurrency): array
    {
        try {
            // Convert historical cost using historical rate
            $conversion = $this->currencyService->calculatePrice(
                $product->historical_cost_amount,
                $product->historical_cost_currency,
                $targetCurrency,
                $product->store_id,
                $product->historical_cost_date
            );

            // Calculate current cost for comparison
            $currentConversion = $this->currencyService->calculatePrice(
                $product->historical_cost_amount,
                $product->historical_cost_currency,
                $targetCurrency,
                $product->store_id
            );

            return [
                'historical_amount' => $conversion['amount'],
                'historical_rate' => $conversion['rate'],
                'historical_date' => $product->historical_cost_date,
                'current_amount' => $currentConversion['amount'],
                'current_rate' => $currentConversion['rate'],
                'difference' => $currentConversion['amount'] - $conversion['amount'],
                'difference_percentage' => $this->calculatePercentageDifference(
                    $conversion['amount'],
                    $currentConversion['amount']
                ),
            ];
        } catch (\Exception $e) {
            return [
                'historical_amount' => null,
                'error' => 'Unable to convert historical cost',
            ];
        }
    }

    /**
     * Calculate profit margin.
     */
    private function calculateMargin(float $revenue, float $cost): float
    {
        if ($revenue == 0) {
            return 0;
        }

        return round((($revenue - $cost) / $revenue) * 100, 2);
    }

    /**
     * Calculate percentage difference.
     */
    private function calculatePercentageDifference(float $oldValue, float $newValue): float
    {
        if ($oldValue == 0) {
            return 0;
        }

        return round((($newValue - $oldValue) / $oldValue) * 100, 2);
    }

    /**
     * Get pricing summary for multiple products.
     */
    public function getPricingSummary(Collection $products, string $targetCurrency): array
    {
        $summary = [
            'total_products' => $products->count(),
            'total_value' => 0,
            'total_cost' => 0,
            'total_margin' => 0,
            'products_on_sale' => 0,
            'out_of_stock' => 0,
            'low_stock' => 0,
        ];

        foreach ($products as $product) {
            $prices = $this->calculateProductPrices($product, $targetCurrency);

            if (isset($prices['price'])) {
                $summary['total_value'] += $prices['price'];
            }

            if (isset($prices['cost_price'])) {
                $summary['total_cost'] += $prices['cost_price'];
            }

            if (isset($prices['margin'])) {
                $summary['total_margin'] += $prices['margin'];
            }

            if ($product->is_on_sale) {
                $summary['products_on_sale']++;
            }

            if (!$product->isInStock()) {
                $summary['out_of_stock']++;
            } elseif ($product->hasLowStock()) {
                $summary['low_stock']++;
            }
        }

        // Calculate average margin
        if ($summary['total_products'] > 0) {
            $summary['average_margin'] = $summary['total_margin'] / $summary['total_products'];
        } else {
            $summary['average_margin'] = 0;
        }

        return $summary;
    }
}
