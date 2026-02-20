<?php

namespace App\Services;

use App\Models\Tenant;

class BrandingService
{
    protected ?Tenant $tenant = null;

    public function __construct()
    {
        if (tenancy()->initialized) {
            $this->tenant = tenant();
        }
    }

    public function getTenant(): ?Tenant
    {
        return $this->tenant;
    }

    public function getBrandingConfig(): array
    {
        if (!$this->tenant) {
            return $this->getDefaultBranding();
        }

        return $this->tenant->branding;
    }

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

    public function getCSSVariables(): string
    {
        $config = $this->getBrandingConfig();
        
        return "
            :root {
                --color-primary: {$config['primary_color']};
                --color-secondary: {$config['secondary_color']};
                --color-accent: {$config['accent_color']};
                --theme-mode: {$config['theme']};
            }
        ";
    }

    public function updateBranding(array $config): bool
    {
        if (!$this->tenant) {
            return false;
        }

        $currentConfig = $this->tenant->branding_config ?? [];
        $newConfig = array_merge($currentConfig, $config);

        return $this->tenant->update(['branding_config' => $newConfig]);
    }

    public function getLogoUrl(): ?string
    {
        $config = $this->getBrandingConfig();
        
        return $config['logo_url'] ?? asset('images/logos/kromerce-business-text.png');
    }

    public function getFaviconUrl(): ?string
    {
        $config = $this->getBrandingConfig();
        
        return $config['favicon_url'] ?? asset('favicon.ico');
    }

    public function getCustomCSS(): ?string
    {
        $config = $this->getBrandingConfig();
        
        return $config['custom_css'] ?? null;
    }

    public function isDarkMode(): bool
    {
        $config = $this->getBrandingConfig();
        
        return ($config['theme'] ?? 'light') === 'dark';
    }
}
