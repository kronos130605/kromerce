<?php

namespace App\Services;

use App\Models\Store;
use App\Repositories\Store\StoreBrandingRepository;

class BrandingService
{
    protected ?Store $store = null;
    private StoreBrandingRepository $brandingRepo;

    public function __construct(StoreBrandingRepository $brandingRepo)
    {
        $this->brandingRepo = $brandingRepo;

        if (tenancy()->initialized) {
            $this->store = tenant();
        }
    }

    public function getStore(): ?Store
    {
        return $this->store;
    }

    public function getBrandingConfig(): array
    {
        if (!$this->store) {
            return $this->brandingRepo->getDefaultBranding();
        }

        return $this->brandingRepo->getBrandingConfig($this->store->id);
    }

    public function getDefaultBranding(): array
    {
        return $this->brandingRepo->getDefaultBranding();
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
        if (!$this->store) {
            return false;
        }

        return $this->brandingRepo->updateBrandingConfig($this->store->id, $config);
    }

    public function getLogoUrl(): ?string
    {
        if (!$this->store) {
            return asset('images/logos/kromerce-business-text.png');
        }

        return $this->brandingRepo->getLogoUrl($this->store->id);
    }

    public function getFaviconUrl(): ?string
    {
        if (!$this->store) {
            return asset('favicon.ico');
        }

        return $this->brandingRepo->getFaviconUrl($this->store->id);
    }

    public function getCustomCSS(): ?string
    {
        if (!$this->store) {
            return null;
        }

        return $this->brandingRepo->getCustomCSS($this->store->id);
    }

    public function isDarkMode(): bool
    {
        if (!$this->store) {
            return false;
        }

        return $this->brandingRepo->isDarkMode($this->store->id);
    }
}
