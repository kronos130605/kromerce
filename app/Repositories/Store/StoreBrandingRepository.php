<?php

namespace App\Repositories\Store;

use App\Models\Store;
use App\Repositories\BaseRepository;

class StoreBrandingRepository extends BaseRepository
{
    public function __construct(Store $model)
    {
        parent::__construct($model);
    }

    /**
     * Get branding configuration for store.
     */
    public function getBrandingConfig(int $storeId): array
    {
        $store = $this->getById($storeId);
        return $store ? $store->branding : $this->getDefaultBranding();
    }

    /**
     * Update branding configuration for store.
     */
    public function updateBrandingConfig(int $storeId, array $config): bool
    {
        $store = $this->getById($storeId);
        if (!$store) {
            return false;
        }

        $currentConfig = $store->branding_config ?? [];
        $newConfig = array_merge($currentConfig, $config);

        return $this->update($storeId, ['branding_config' => $newConfig]);
    }

    /**
     * Get logo URL for store.
     */
    public function getLogoUrl(int $storeId): ?string
    {
        $config = $this->getBrandingConfig($storeId);
        return $config['logo_url'] ?? asset('images/logos/kromerce-business-text.png');
    }

    /**
     * Get favicon URL for store.
     */
    public function getFaviconUrl(int $storeId): ?string
    {
        $config = $this->getBrandingConfig($storeId);
        return $config['favicon_url'] ?? asset('favicon.ico');
    }

    /**
     * Get custom CSS for store.
     */
    public function getCustomCSS(int $storeId): ?string
    {
        $config = $this->getBrandingConfig($storeId);
        return $config['custom_css'] ?? null;
    }

    /**
     * Check if store uses dark mode.
     */
    public function isDarkMode(int $storeId): bool
    {
        $config = $this->getBrandingConfig($storeId);
        return ($config['theme'] ?? 'light') === 'dark';
    }

    /**
     * Get default branding configuration.
     */
    public function getDefaultBranding(): array
    {
        return [
            'primary_color' => '#3B82F6',
            'secondary_color' => '#10B981',
            'accent_color' => '#F59E0B',
            'logo_url' => null,
            'favicon_url' => null,
            'custom_css' => null,
            'theme' => 'light',
        ];
    }
}
