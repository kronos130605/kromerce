<?php

namespace App\Http\Controllers\Business;

use App\Helpers\TranslationHelper;
use App\Http\Controllers\Controller;
use App\Repositories\Store\BusinessCurrencyConfigRepository;
use App\Repositories\Store\CurrencySourceRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    public function __construct(
        private CurrencySourceRepository $sourceRepo,
        private BusinessCurrencyConfigRepository $configRepo,
    ) {}

    /**
     * Display the settings page.
     */
    public function index(): Response
    {
        try {
            $store = $this->validateStore();
            $config = $this->configRepo->getOrCreateForStore($store->id);

            $sources = $this->sourceRepo->getActive()->map(fn ($source) => [
                'id'               => $source->id,
                'name'             => $source->name,
                'code'             => $source->code,
                'type'             => $source->type,
                'type_label'       => $source->type === 'api' ? 'API REST' : 'Web Scraping',
                'is_global_default' => $source->is_global_default,
                'success_rate'     => $source->getSuccessRate(),
                'last_tested_at'   => $source->last_tested_at?->diffForHumans(),
                'last_test_success' => $source->last_test_success,
            ]);

            return Inertia::render('Business/Index', [
                'activeTab'    => 'settings',
                'translations' => TranslationHelper::forPreset('settings'),
                'settings'     => [
                    'currency' => [
                        'sources'                  => $sources,
                        'preferred_cuba_source_id' => $config->preferred_cuba_source_id,
                        'default_currency'         => $config->default_currency,
                        'display_currencies'       => $config->display_currencies ?? [],
                        'auto_update_rates'        => $config->auto_update_rates,
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('SettingsController::index', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Update the preferred CUP rate source for the store.
     */
    public function updateCurrencySource(Request $request): JsonResponse
    {
        try {
            $store = $this->validateStore();

            $validated = $request->validate([
                'preferred_cuba_source_id' => 'nullable|uuid|exists:currency_sources,id',
            ]);

            $this->configRepo->updateBy(
                ['store_id' => $store->id],
                ['preferred_cuba_source_id' => $validated['preferred_cuba_source_id']]
            );

            return $this->success([], 'Currency source updated');

        } catch (\Exception $e) {
            Log::error('SettingsController::updateCurrencySource', ['error' => $e->getMessage()]);
            return $this->error('Update failed: ' . $e->getMessage(), 500);
        }
    }
}
