<?php

namespace App\Services;

use App\Models\StoreActiveCurrency;
use App\Repositories\Store\StoreActiveCurrencyRepository;
use App\Repositories\Store\StoreCurrencyConfigRepository;
use App\Repositories\Product\ProductRepository;
use App\Repositories\Product\ProductSaleCurrencyRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoreCurrencyService
{
    public function __construct(
        private StoreActiveCurrencyRepository $activeCurrencyRepo,
        private StoreCurrencyConfigRepository $configRepo,
        private ProductRepository $productRepo,
        private ProductSaleCurrencyRepository $saleCurrencyRepo,
        private CurrencyRateService $rateService
    ) {}

    /**
     * Get all active currencies for a store with metadata.
     */
    public function getActiveCurrenciesForStore(int|string $storeId): array
    {
        $records = $this->activeCurrencyRepo->getAllForStore($storeId);
        $supported = config('currencies.supported', []);

        return $records->map(function (StoreActiveCurrency $record) use ($supported, $storeId) {
            $meta = $supported[$record->currency_code] ?? [];
            return [
                'id'            => $record->id,
                'code'          => $record->currency_code,
                'name'          => $meta['name'] ?? $record->currency_code,
                'symbol'        => $meta['symbol'] ?? $record->currency_code,
                'flag'          => $meta['flag'] ?? null,
                'is_active'     => $record->is_active,
                'sort_order'    => $record->sort_order,
                'is_cup'        => $record->currency_code === 'CUP',
                'is_cla'        => $record->currency_code === 'CLA',
            ];
        })->values()->toArray();
    }

    /**
     * Get all supported currencies with their activation status for the store.
     */
    public function getSupportedCurrenciesWithStatus(int|string $storeId): array
    {
        $supported = config('currencies.supported', []);
        $activeCodes = $this->activeCurrencyRepo->getActiveCodesForStore($storeId);
        $allRecords  = $this->activeCurrencyRepo->getAllForStore($storeId)
            ->keyBy('currency_code');

        return collect($supported)->map(function (array $meta, string $code) use ($activeCodes, $allRecords) {
            $record = $allRecords->get($code);
            return [
                'code'       => $code,
                'name'       => $meta['name'],
                'symbol'     => $meta['symbol'],
                'flag'       => $meta['flag'] ?? null,
                'is_active'  => in_array($code, $activeCodes),
                'sort_order' => $record?->sort_order ?? 0,
                'is_cup'     => $code === 'CUP',
                'is_cla'     => $code === 'CLA',
            ];
        })->values()->toArray();
    }

    /**
     * Activate a currency for a store.
     */
    public function activateCurrency(int|string $storeId, string $currencyCode): StoreActiveCurrency
    {
        $supported = array_keys(config('currencies.supported', []));

        if (!in_array($currencyCode, $supported)) {
            throw new \InvalidArgumentException("Currency {$currencyCode} is not supported.");
        }

        return $this->activeCurrencyRepo->updateOrCreate(
            ['store_id' => $storeId, 'currency_code' => $currencyCode],
            ['is_active' => true]
        );
    }

    /**
     * Deactivate a currency for a store.
     * Returns info about affected products if any (caller decides what to do).
     */
    public function deactivateCurrency(int|string $storeId, string $currencyCode): array
    {
        $affectedCount = $this->saleCurrencyRepo->countActiveProductsWithCurrency($storeId, $currencyCode);

        if ($affectedCount > 0) {
            $affectedProducts = $this->saleCurrencyRepo
                ->getActiveProductsWithCurrency($storeId, $currencyCode)
                ->map(fn ($r) => [
                    'id'            => $r->product->id,
                    'name'          => $r->product->name,
                    'sku'           => $r->product->sku,
                    'base_currency' => $r->product->base_currency,
                ])
                ->toArray();

            return [
                'can_deactivate'   => false,
                'affected_count'   => $affectedCount,
                'affected_products' => $affectedProducts,
                'message'          => "There are {$affectedCount} active products using this currency.",
            ];
        }

        $this->activeCurrencyRepo->updateBy(
            ['store_id' => $storeId, 'currency_code' => $currencyCode],
            ['is_active' => false]
        );

        return [
            'can_deactivate' => true,
            'affected_count' => 0,
            'message'        => 'Currency deactivated successfully.',
        ];
    }

    /**
     * Force deactivate a currency, migrating affected products to another currency.
     */
    public function deactivateCurrencyWithMigration(
        int|string $storeId,
        string $currencyCode,
        string $migrateToCurrency
    ): array {
        if (!$this->activeCurrencyRepo->isCurrencyActiveForStore($storeId, $migrateToCurrency)) {
            throw new \InvalidArgumentException("Target currency {$migrateToCurrency} is not active for this store.");
        }

        DB::beginTransaction();
        try {
            $migrated = $this->saleCurrencyRepo->migrateCurrencyForStore($storeId, $currencyCode, $migrateToCurrency);

            $this->activeCurrencyRepo->updateBy(
                ['store_id' => $storeId, 'currency_code' => $currencyCode],
                ['is_active' => false]
            );

            DB::commit();

            return [
                'success'          => true,
                'migrated_products' => $migrated,
                'message'          => "Migrated {$migrated} products from {$currencyCode} to {$migrateToCurrency}.",
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('StoreCurrencyService::deactivateCurrencyWithMigration', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Store active currencies for a new store (called on store creation).
     */
    public function initializeDefaultCurrencies(int|string $storeId, array $currencyCodes = []): void
    {
        if (empty($currencyCodes)) {
            $currencyCodes = array_keys(config('currencies.supported', []));
        }

        foreach ($currencyCodes as $index => $code) {
            $this->activeCurrencyRepo->updateOrCreate(
                ['store_id' => $storeId, 'currency_code' => $code],
                ['is_active' => true, 'sort_order' => $index]
            );
        }
    }

    /**
     * Compute CUP/CLA converted cost for a product at purchase time.
     * Returns array with cup and cla conversion data (null if not applicable).
     */
    public function computeCupClaCostConversion(
        int|string $storeId,
        float $costAmount,
        string $costCurrency
    ): array {
        if (in_array($costCurrency, ['CUP', 'CLA'])) {
            return ['cup' => null, 'cla' => null];
        }

        $activeCodes = $this->activeCurrencyRepo->getActiveCodesForStore($storeId);
        $today = now()->format('Y-m-d');

        $cup = null;
        $cla = null;

        if (in_array('CUP', $activeCodes)) {
            try {
                $conversion = $this->rateService->calculatePrice($costAmount, $costCurrency, 'CUP', $storeId);
                $cup = [
                    'amount' => $conversion['amount'],
                    'rate'   => $conversion['rate'],
                    'date'   => $today,
                ];
            } catch (\Exception $e) {
                Log::warning('Could not convert cost to CUP', [
                    'store_id' => $storeId,
                    'currency' => $costCurrency,
                    'error'    => $e->getMessage(),
                ]);
            }
        }

        if (in_array('CLA', $activeCodes)) {
            try {
                $conversion = $this->rateService->calculatePrice($costAmount, $costCurrency, 'CLA', $storeId);
                $cla = [
                    'amount' => $conversion['amount'],
                    'rate'   => $conversion['rate'],
                    'date'   => $today,
                ];
            } catch (\Exception $e) {
                Log::warning('Could not convert cost to CLA', [
                    'store_id' => $storeId,
                    'currency' => $costCurrency,
                    'error'    => $e->getMessage(),
                ]);
            }
        }

        return ['cup' => $cup, 'cla' => $cla];
    }
}
