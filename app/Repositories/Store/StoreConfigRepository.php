<?php

namespace App\Repositories\Store;

use App\Models\Store;
use App\Repositories\BaseRepository;

class StoreConfigRepository extends BaseRepository
{
    public function __construct(Store $model)
    {
        parent::__construct($model);
    }

    public function getStoreConfig(int $storeId): array
    {
        $store = $this->getById($storeId);

        if (!$store) {
            return $this->getDefaultConfig();
        }

        return array_merge([
            'id' => $store->id,
            'name' => $store->name,
            'slug' => $store->slug,
            'type' => $store->type,
            'is_active' => $store->is_active,
        ], $store->settings ?? []);
    }

    public function updateStoreConfig(int $storeId, array $config): bool
    {
        $store = $this->getById($storeId);
        if (!$store) {
            return false;
        }

        $store->settings = array_merge($store->settings ?? [], $config);
        return $store->save();
    }

    public function getThemeConfig(int $storeId): array
    {
        $config = $this->getStoreConfig($storeId);

        return [
            'theme' => $config['theme'] ?? 'default',
            'primary_color' => $config['primary_color'] ?? '#3B82F6',
            'secondary_color' => $config['secondary_color'] ?? '#10B981',
            'accent_color' => $config['accent_color'] ?? '#F59E0B',
            'special_events' => $config['special_events'] ?? [],
        ];
    }

    public function getFeatureFlags(int $storeId): array
    {
        $config = $this->getStoreConfig($storeId);

        return [
            'show_flash_sales' => $config['show_flash_sales'] ?? true,
            'show_featured_stores' => $config['show_featured_stores'] ?? true,
            'show_ai_recommendations' => $config['show_ai_recommendations'] ?? true,
            'enable_notifications' => $config['enable_notifications'] ?? true,
            'enable_wishlist' => $config['enable_wishlist'] ?? true,
            'enable_reviews' => $config['enable_reviews'] ?? true,
        ];
    }

    public function getLayoutConfig(int $storeId): array
    {
        $config = $this->getStoreConfig($storeId);

        return [
            'sidebar_position' => $config['layout']['sidebar_position'] ?? 'left',
            'product_grid_columns' => $config['layout']['product_grid_columns'] ?? 4,
            'show_product_ratings' => $config['layout']['show_product_ratings'] ?? true,
            'show_product_compare' => $config['layout']['show_product_compare'] ?? true,
        ];
    }

    public function hasSeasonalTheme(int $storeId): bool
    {
        $config = $this->getStoreConfig($storeId);
        $specialEvents = $config['special_events'] ?? [];

        return isset($specialEvents['current_season']) &&
               isset($specialEvents['season_start']) &&
               $specialEvents['season_start'] <= now()->toDateString();
    }

    public function getSeasonalConfig(int $storeId): array
    {
        $config = $this->getStoreConfig($storeId);
        $specialEvents = $config['special_events'] ?? [];

        if ($this->hasSeasonalTheme($storeId)) {
            return $specialEvents['season_config'] ?? [];
        }

        return [];
    }

    public function getDefaultConfig(): array
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
}
