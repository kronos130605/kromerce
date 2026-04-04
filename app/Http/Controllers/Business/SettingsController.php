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

            $allSources = $this->sourceRepo->getActive();

            $mapSource = fn ($source) => [
                'id'                => $source->id,
                'name'              => $source->name,
                'code'              => $source->code,
                'type'              => $source->type,
                'type_label'        => $source->type === 'api' ? 'API REST' : 'Web Scraping',
                'is_global_default' => $source->is_global_default,
                'success_rate'      => $source->getSuccessRate(),
                'last_tested_at'    => $source->last_tested_at?->diffForHumans(),
                'last_test_success' => $source->last_test_success,
                'supported_currencies' => $source->supported_currencies ?? [],
            ];

            // Fuentes que soportan CUP (ElToque, BCC)
            $cupSources = $allSources
                ->filter(fn ($s) => in_array('CUP', $s->supported_currencies ?? []))
                ->map($mapSource)
                ->values();

            // Fuentes para divisas extranjeras (NO soportan CUP - o solo divisas)
            $foreignSources = $allSources
                ->filter(fn ($s) => !in_array('CUP', $s->supported_currencies ?? []))
                ->map($mapSource)
                ->values();

            return Inertia::render('Business/Index', [
                'activeTab'    => 'settings',
                'translations' => TranslationHelper::forPreset('settings'),
                'settings'     => [
                    'currency' => [
                        'cup_sources'               => $cupSources,
                        'foreign_sources'           => $foreignSources,
                        'preferred_cuba_source_id'  => $config->preferred_cuba_source_id,
                        'preferred_foreign_source_id' => $config->preferred_foreign_source_id,
                        'default_currency'          => $config->default_currency,
                        'display_currencies'        => $config->display_currencies ?? [],
                        'auto_update_rates'       => $config->auto_update_rates,
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('SettingsController::index', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Update the preferred rate sources for the store.
     */
    public function updateCurrencySource(Request $request): JsonResponse
    {
        try {
            $store = $this->validateStore();

            $validated = $request->validate([
                'preferred_cuba_source_id'    => 'nullable|uuid|exists:currency_sources,id',
                'preferred_foreign_source_id' => 'nullable|uuid|exists:currency_sources,id',
                'type'                        => 'required|in:cup,foreign',
            ]);

            $updateData = [];

            if ($validated['type'] === 'cup' && isset($validated['preferred_cuba_source_id'])) {
                $updateData['preferred_cuba_source_id'] = $validated['preferred_cuba_source_id'];
            }

            if ($validated['type'] === 'foreign' && isset($validated['preferred_foreign_source_id'])) {
                $updateData['preferred_foreign_source_id'] = $validated['preferred_foreign_source_id'];
            }

            if (!empty($updateData)) {
                $this->configRepo->updateBy(['store_id' => $store->id], $updateData);
            }

            return $this->success([], 'Currency source updated');

        } catch (\Exception $e) {
            Log::error('SettingsController::updateCurrencySource', ['error' => $e->getMessage()]);
            return $this->error('Update failed: ' . $e->getMessage(), 500);
        }
    }
}
