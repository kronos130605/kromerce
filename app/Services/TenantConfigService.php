<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TenantConfigService
{
    /**
     * Get tenant configuration with caching
     */
    public function getTenantConfig(Tenant $tenant): array
    {
        try {
            $cacheKey = "tenant_config_{$tenant->id}";
            
            return Cache::remember($cacheKey, 3600, function () use ($tenant) {
                return array_merge([
                    'id' => $tenant->id,
                    'name' => $tenant->name,
                    'slug' => $tenant->slug,
                    'type' => $tenant->type,
                    'is_active' => $tenant->is_active,
                ], $tenant->settings ?? []);
            });
        } catch (\Exception $e) {
            Log::error('Error getting tenant config', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);
            
            return $this->getDefaultConfig();
        }
    }

    /**
     * Update tenant configuration
     */
    public function updateTenantConfig(Tenant $tenant, array $config): bool
    {
        try {
            $tenant->settings = array_merge($tenant->settings ?? [], $config);
            $tenant->save();
            
            // Clear cache
            Cache::forget("tenant_config_{$tenant->id}");
            
            Log::info('Tenant config updated', [
                'tenant_id' => $tenant->id,
                'config_changes' => $config,
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Error updating tenant config', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Get theme configuration for frontend
     */
    public function getThemeConfig(Tenant $tenant): array
    {
        $config = $this->getTenantConfig($tenant);
        
        return [
            'theme' => $config['theme'] ?? 'default',
            'primary_color' => $config['primary_color'] ?? '#3B82F6',
            'secondary_color' => $config['secondary_color'] ?? '#10B981',
            'accent_color' => $config['accent_color'] ?? '#F59E0B',
            'special_events' => $config['special_events'] ?? [],
        ];
    }

    /**
     * Get feature flags for tenant
     */
    public function getFeatureFlags(Tenant $tenant): array
    {
        $config = $this->getTenantConfig($tenant);
        
        return [
            'show_flash_sales' => $config['show_flash_sales'] ?? true,
            'show_featured_stores' => $config['show_featured_stores'] ?? true,
            'show_ai_recommendations' => $config['show_ai_recommendations'] ?? true,
            'enable_notifications' => $config['enable_notifications'] ?? true,
            'enable_wishlist' => $config['enable_wishlist'] ?? true,
            'enable_reviews' => $config['enable_reviews'] ?? true,
        ];
    }

    /**
     * Get layout configuration for tenant
     */
    public function getLayoutConfig(Tenant $tenant): array
    {
        $config = $this->getTenantConfig($tenant);
        
        return [
            'sidebar_position' => $config['layout']['sidebar_position'] ?? 'left',
            'product_grid_columns' => $config['layout']['product_grid_columns'] ?? 4,
            'show_product_ratings' => $config['layout']['show_product_ratings'] ?? true,
            'show_product_compare' => $config['layout']['show_product_compare'] ?? true,
        ];
    }

    /**
     * Update seasonal theme
     */
    public function updateSeasonalTheme(Tenant $tenant, string $theme, array $themeConfig): bool
    {
        $seasonalConfig = [
            'theme' => $theme,
            'special_events' => array_merge(
                $this->getTenantConfig($tenant)['special_events'] ?? [],
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

        return $this->updateTenantConfig($tenant, $seasonalConfig);
    }

    /**
     * Get default configuration
     */
    private function getDefaultConfig(): array
    {
        return [
            'theme' => 'default',
            'primary_color' => '#3B82F6',
            'secondary_color' => '#10B981',
            'accent_color' => '#F59E0B',
            'show_flash_sales' => true,
            'show_featured_stores' => true,
            'show_ai_recommendations' => true,
            'default_currency' => 'USD',
            'language' => 'es',
            'timezone' => 'America/Mexico_City',
            'enable_notifications' => true,
            'enable_wishlist' => true,
            'enable_reviews' => true,
            'special_events' => [],
            'layout' => [
                'sidebar_position' => 'left',
                'product_grid_columns' => 4,
                'show_product_ratings' => true,
                'show_product_compare' => true,
            ],
        ];
    }

    /**
     * Check if tenant has seasonal theme active
     */
    public function hasSeasonalTheme(Tenant $tenant): bool
    {
        $config = $this->getTenantConfig($tenant);
        $specialEvents = $config['special_events'] ?? [];
        
        return isset($specialEvents['current_season']) && 
               isset($specialEvents['season_start']) &&
               $specialEvents['season_start'] <= now()->toDateString();
    }

    /**
     * Get seasonal configuration
     */
    public function getSeasonalConfig(Tenant $tenant): array
    {
        $config = $this->getTenantConfig($tenant);
        $specialEvents = $config['special_events'] ?? [];
        
        if ($this->hasSeasonalTheme($tenant)) {
            return $specialEvents['season_config'] ?? [];
        }
        
        return [];
    }
}
