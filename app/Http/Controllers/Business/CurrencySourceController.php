<?php

namespace App\Http\Controllers\Business;

use App\Helpers\TranslationHelper;
use App\Http\Controllers\Controller;
use App\Repositories\Store\StoreCurrencyConfigRepository;
use App\Repositories\Store\CurrencySourceRepository;
use App\Services\CurrencyRateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class CurrencySourceController extends Controller
{
    public function __construct(
        private CurrencySourceRepository $sourceRepo,
        private StoreCurrencyConfigRepository $configRepo,
        private CurrencyRateService $currencyService
    ) {
        $this->middleware('role:business_owner');
    }

    /**
     * Display currency source selection for the store.
     */
    public function index(): Response
    {
        try {
            $store = $this->validateStore();
            $config = $this->configRepo->getFirstBy(['store_id' => $store->id]);

            $sources = $this->sourceRepo->getActive()->map(fn ($source) => [
                'id' => $source->id,
                'name' => $source->name,
                'code' => $source->code,
                'type' => $source->type,
                'type_label' => $source->type === 'api' ? 'API REST' : 'Web Scraping',
                'is_global_default' => $source->is_global_default,
                'success_rate' => $source->getSuccessRate(),
                'last_tested_at' => $source->last_tested_at?->diffForHumans(),
                'requires_auth' => $source->auth_type !== 'none',
            ]);

            return Inertia::render('settings/CurrencySource', [
                'sources' => $sources,
                'current_source_id' => $config?->source_id,
                'use_global' => !$config?->source_id,
                'translations' => TranslationHelper::forPreset('currency_settings'),
            ]);

        } catch (\Exception $e) {
            Log::error('CurrencySourceController::index', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Update store's currency source.
     */
    public function update(Request $request): JsonResponse
    {
        try {
            $store = $this->validateStore();
            $config = $this->configRepo->getOrCreateForStore($store->id);

            $validated = $request->validate([
                'source_id' => 'nullable|uuid|exists:currency_sources,id',
                'use_global' => 'boolean',
                'source_config_override' => 'nullable|array',
            ]);

            if ($validated['use_global'] ?? false) {
                $this->configRepo->updateBy(['store_id' => $store->id], [
                    'source_id' => null,
                    'source_config_override' => null,
                ]);

                return $this->success(['use_global' => true], 'Using global currency rates');
            }

            $source = $this->sourceRepo->find($validated['source_id']);
            if (!$source || !$source->is_active) {
                return $this->error('Source not available', 400);
            }

            // Test connection if credentials provided
            if (!empty($validated['source_config_override'])) {
                $test = $this->currencyService->testSourceConnection(
                    $source->id,
                    $validated['source_config_override']
                );

                if (!$test['success']) {
                    return $this->error('Connection failed: ' . $test['message'], 400, ['test' => $test]);
                }
            }

            $this->configRepo->updateBy(['store_id' => $store->id], [
                'source_id' => $validated['source_id'],
                'source_config_override' => $validated['source_config_override'] ?? null,
            ]);

            return $this->success(['source_name' => $source->name], 'Currency source updated');

        } catch (\Exception $e) {
            Log::error('CurrencySourceController::update', ['error' => $e->getMessage()]);
            return $this->error('Update failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Test connection to a source.
     */
    public function test(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'source_id' => 'required|uuid|exists:currency_sources,id',
                'source_config_override' => 'nullable|array',
            ]);

            $result = $this->currencyService->testSourceConnection(
                $validated['source_id'],
                $validated['source_config_override'] ?? null
            );

            return $this->success($result, $result['success'] ? 'Connection OK' : 'Connection failed');

        } catch (\Exception $e) {
            Log::error('CurrencySourceController::test', ['error' => $e->getMessage()]);
            return $this->error('Test failed: ' . $e->getMessage(), 500);
        }
    }
}
