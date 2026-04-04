<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Repositories\Product\ProductSaleCurrencyRepository;
use App\Repositories\Store\StoreActiveCurrencyRepository;
use App\Services\StoreCurrencyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StoreCurrencyController extends Controller
{
    public function __construct(
        private StoreCurrencyService $currencyService,
        private StoreActiveCurrencyRepository $activeCurrencyRepo,
        private ProductSaleCurrencyRepository $saleCurrencyRepo
    ) {
        $this->middleware('role:business_owner');
    }

    /**
     * Get all supported currencies with their activation status for the store.
     */
    public function index(): JsonResponse
    {
        try {
            $store = $this->validateStore();
            $currencies = $this->currencyService->getSupportedCurrenciesWithStatus($store->id);

            return $this->success(['currencies' => $currencies]);
        } catch (\Exception $e) {
            Log::error('StoreCurrencyController::index', ['error' => $e->getMessage()]);
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Activate a currency for the store.
     */
    public function activate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'currency_code' => 'required|string|size:3',
        ]);

        try {
            $store = $this->validateStore();
            $record = $this->currencyService->activateCurrency($store->id, $validated['currency_code']);

            return $this->success(
                ['currency' => $record->currency_code],
                "Currency {$validated['currency_code']} activated."
            );
        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 422);
        } catch (\Exception $e) {
            Log::error('StoreCurrencyController::activate', ['error' => $e->getMessage()]);
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Attempt to deactivate a currency (returns info if products are affected).
     * Pass force=true to disable all affected sale currencies automatically.
     */
    public function deactivate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'currency_code' => 'required|string|size:3',
            'force'         => 'boolean',
        ]);

        try {
            $store = $this->validateStore();

            if (!empty($validated['force'])) {
                $this->saleCurrencyRepo->disableCurrencyForStore($store->id, $validated['currency_code']);
                $this->activeCurrencyRepo->updateBy(
                    ['store_id' => $store->id, 'currency_code' => $validated['currency_code']],
                    ['is_active' => false]
                );
                return $this->success([], 'Currency force-deactivated.');
            }

            $result = $this->currencyService->deactivateCurrency($store->id, $validated['currency_code']);

            if (!$result['can_deactivate']) {
                return response()->json([
                    'success'           => false,
                    'requires_action'   => true,
                    'affected_count'    => $result['affected_count'],
                    'affected_products' => $result['affected_products'],
                    'message'           => $result['message'],
                ], 409);
            }

            return $this->success([], $result['message']);
        } catch (\Exception $e) {
            Log::error('StoreCurrencyController::deactivate', ['error' => $e->getMessage()]);
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Force deactivate a currency by migrating affected products to another currency.
     */
    public function deactivateWithMigration(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'currency_code'      => 'required|string|size:3',
            'migrate_to_currency' => 'required|string|size:3|different:currency_code',
        ]);

        try {
            $store = $this->validateStore();
            $result = $this->currencyService->deactivateCurrencyWithMigration(
                $store->id,
                $validated['currency_code'],
                $validated['migrate_to_currency']
            );

            return $this->success(['migrated_products' => $result['migrated_products']], $result['message']);
        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 422);
        } catch (\Exception $e) {
            Log::error('StoreCurrencyController::deactivateWithMigration', ['error' => $e->getMessage()]);
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Reorder active currencies for the store.
     */
    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order'   => 'required|array|min:1',
            'order.*' => 'required|string|size:3',
        ]);

        try {
            $store = $this->validateStore();

            foreach ($validated['order'] as $index => $code) {
                $this->activeCurrencyRepo->updateBy(
                    ['store_id' => $store->id, 'currency_code' => $code],
                    ['sort_order' => $index]
                );
            }

            return $this->success([], 'Currency order updated.');
        } catch (\Exception $e) {
            Log::error('StoreCurrencyController::reorder', ['error' => $e->getMessage()]);
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Get sale currencies for a specific product.
     */
    public function productSaleCurrencies(Request $request, string $productId): JsonResponse
    {
        try {
            $store = $this->validateStore();
            $activeCodes = $this->activeCurrencyRepo->getActiveCodesForStore($store->id);
            $enabledCodes = $this->saleCurrencyRepo->getEnabledCodesForProduct($productId);
            $supported = config('currencies.supported', []);

            $currencies = collect($activeCodes)->map(fn ($code) => [
                'code'       => $code,
                'name'       => $supported[$code]['name'] ?? $code,
                'symbol'     => $supported[$code]['symbol'] ?? $code,
                'is_enabled' => in_array($code, $enabledCodes),
            ])->values()->toArray();

            return $this->success(['currencies' => $currencies]);
        } catch (\Exception $e) {
            Log::error('StoreCurrencyController::productSaleCurrencies', ['error' => $e->getMessage()]);
            return $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Update sale currencies for a specific product.
     */
    public function updateProductSaleCurrencies(Request $request, string $productId): JsonResponse
    {
        $validated = $request->validate([
            'currencies'   => 'required|array',
            'currencies.*' => 'string|size:3',
        ]);

        try {
            $store = $this->validateStore();
            $activeCodes = $this->activeCurrencyRepo->getActiveCodesForStore($store->id);

            $allowed = array_intersect($validated['currencies'], $activeCodes);
            $this->saleCurrencyRepo->syncForProduct($productId, $allowed);

            return $this->success(['enabled' => $allowed], 'Sale currencies updated.');
        } catch (\Exception $e) {
            Log::error('StoreCurrencyController::updateProductSaleCurrencies', ['error' => $e->getMessage()]);
            return $this->error($e->getMessage(), 500);
        }
    }
}
