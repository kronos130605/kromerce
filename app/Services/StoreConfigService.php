<?php

namespace App\Services;

use App\Models\Store;
use App\Repositories\Store\StoreConfigRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class StoreConfigService
{
    private StoreConfigRepository $configRepo;

    public function __construct(StoreConfigRepository $configRepo)
    {
        $this->configRepo = $configRepo;
    }

    /**
     * Get store configuration with caching
     */
    public function getStoreConfig(Store $store): array
    {
        try {
            $cacheKey = "store_config_{$store->id}";

            return Cache::remember($cacheKey, 3600, function () use ($store) {
                return $this->configRepo->getStoreConfig($store->id);
            });
        } catch (\Exception $e) {
            Log::error('Error getting store config', [
                'store_id' => $store->id,
                'error' => $e->getMessage(),
            ]);

            return $this->configRepo->getDefaultConfig();
        }
    }

    /**
     * Update store configuration
     */
    public function updateStoreConfig(Store $store, array $config): bool
    {
        try {
            $result = $this->configRepo->updateStoreConfig($store->id, $config);

            // Clear cache
            Cache::forget("store_config_{$store->id}");

            Log::info('Store config updated', [
                'store_id' => $store->id,
                'config_changes' => $config,
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('Error updating store config', [
                'store_id' => $store->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get theme configuration for frontend
     */
    public function getThemeConfig(Store $store): array
    {
        return $this->configRepo->getThemeConfig($store->id);
    }

    /**
     * Get feature flags for store
     */
    public function getFeatureFlags(Store $store): array
    {
        return $this->configRepo->getFeatureFlags($store->id);
    }

    /**
     * Get layout configuration for store
     */
    public function getLayoutConfig(Store $store): array
    {
        return $this->configRepo->getLayoutConfig($store->id);
    }

    /**
     * Update seasonal theme
     */
    public function updateSeasonalTheme(Store $store, string $theme, array $themeConfig): bool
    {
        $seasonalConfig = [
            'theme' => $theme,
            'special_events' => array_merge(
                $this->getStoreConfig($store)['special_events'] ?? [],
                [
                    'current_season' => $theme,
                    'season_start' => now()->toDateString(),
                    'season_config' => $themeConfig,
                ]
            ),
        ];

        // Add seasonal colors if provided
        if (isset($themeConfig['primary_color'])) {
            $seasonalConfig['primary_color'] = $themeConfig['primary_color'];
        }
        if (isset($themeConfig['secondary_color'])) {
            $seasonalConfig['secondary_color'] = $themeConfig['secondary_color'];
        }
        if (isset($themeConfig['accent_color'])) {
            $seasonalConfig['accent_color'] = $themeConfig['accent_color'];
        }

        return $this->updateStoreConfig($store, $seasonalConfig);
    }

    /**
     * Check if store has seasonal theme active
     */
    public function hasSeasonalTheme(Store $store): bool
    {
        return $this->configRepo->hasSeasonalTheme($store->id);
    }

    /**
     * Get seasonal configuration
     */
    public function getSeasonalConfig(Store $store): array
    {
        return $this->configRepo->getSeasonalConfig($store->id);
    }
}
