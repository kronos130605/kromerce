<?php

namespace App\Providers;

use App\Services\BrandingService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class BrandingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(BrandingService::class, function ($app) {
            return new BrandingService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share branding data with all views
        View::composer('*', function ($view) {
            $branding = app(BrandingService::class);
            
            $view->with([
                'branding' => $branding->getBrandingConfig(),
                'css_variables' => $branding->getCSSVariables(),
                'logo_url' => $branding->getLogoUrl(),
                'favicon_url' => $branding->getFaviconUrl(),
                'custom_css' => $branding->getCustomCSS(),
                'is_dark_mode' => $branding->isDarkMode(),
                'current_tenant' => $branding->getTenant(),
            ]);
        });
    }
}
