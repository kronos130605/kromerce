<?php

namespace App\Providers;

use App\Services\CurrencyRateService;
use App\Services\ProductPricingService;
use Illuminate\Support\ServiceProvider;

class BusinessServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register Currency Rate Service
        $this->app->singleton(CurrencyRateService::class, function ($app) {
            return new CurrencyRateService();
        });

        // Register Product Pricing Service
        $this->app->singleton(ProductPricingService::class, function ($app) {
            return new ProductPricingService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register model relationships
        $this->registerModelRelationships();
        
        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\UpdateCurrencyRates::class,
                \App\Console\Commands\CleanupOldCurrencyRates::class,
            ]);
        }
    }

    /**
     * Register additional model relationships.
     */
    private function registerModelRelationships(): void
    {
        // Tenant relationship with currency config
        \App\Models\Tenant::resolveRelationUsing('currencyConfig', function ($tenant) {
            return $tenant->hasOne(BusinessCurrencyConfig::class);
        });

        // User relationship with tenant (if not already defined)
        \App\Models\User::resolveRelationUsing('tenant', function ($user) {
            return $user->belongsTo(\App\Models\Tenant::class);
        });
    }
}
