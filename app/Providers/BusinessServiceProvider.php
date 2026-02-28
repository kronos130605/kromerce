<?php

namespace App\Providers;

use App\Services\CurrencyRateService;
use App\Services\ProductPricingService;
use App\Repositories\BaseRepository;
use App\Repositories\BusinessCurrencyConfigRepository;
use App\Repositories\CurrencyRateGlobalRepository;
use App\Repositories\CurrencyRateBusinessRepository;
use App\Repositories\CurrencyRateUpdateRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ProductCategoryRepository;
use App\Repositories\ProductTagRepository;
use App\Models\BusinessCurrencyConfig;
use App\Models\CurrencyRateGlobal;
use App\Models\CurrencyRateBusiness;
use App\Models\CurrencyRateUpdate;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductTag;
use Illuminate\Support\ServiceProvider;

class BusinessServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register Repositories
        $this->registerRepositories();
        
        // Register Services
        $this->registerServices();
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
     * Register repositories.
     */
    private function registerRepositories(): void
    {
        // Base Repository
        $this->app->bind(BaseRepository::class, function ($app) {
            // Base repository is abstract, should not be instantiated directly
            return new class extends BaseRepository {
                public function __construct() {
                    // This is just for binding, actual repositories will extend this
                }
            };
        });

        // Currency Repositories
        $this->app->bind(BusinessCurrencyConfigRepository::class, function ($app) {
            return new BusinessCurrencyConfigRepository(new BusinessCurrencyConfig());
        });

        $this->app->bind(CurrencyRateGlobalRepository::class, function ($app) {
            return new CurrencyRateGlobalRepository(new CurrencyRateGlobal());
        });

        $this->app->bind(CurrencyRateBusinessRepository::class, function ($app) {
            return new CurrencyRateBusinessRepository(new CurrencyRateBusiness());
        });

        $this->app->bind(CurrencyRateUpdateRepository::class, function ($app) {
            return new CurrencyRateUpdateRepository(new CurrencyRateUpdate());
        });

        // Product Repositories
        $this->app->bind(ProductRepository::class, function ($app) {
            return new ProductRepository(new Product());
        });

        $this->app->bind(ProductCategoryRepository::class, function ($app) {
            return new ProductCategoryRepository(new ProductCategory());
        });

        $this->app->bind(ProductTagRepository::class, function ($app) {
            return new ProductTagRepository(new ProductTag());
        });
    }

    /**
     * Register services.
     */
    private function registerServices(): void
    {
        // Register Currency Rate Service with repositories
        $this->app->singleton(CurrencyRateService::class, function ($app) {
            return new CurrencyRateService(
                $app->make(BusinessCurrencyConfigRepository::class),
                $app->make(CurrencyRateGlobalRepository::class),
                $app->make(CurrencyRateBusinessRepository::class),
                $app->make(CurrencyRateUpdateRepository::class)
            );
        });

        // Register Product Pricing Service with repositories
        $this->app->singleton(ProductPricingService::class, function ($app) {
            return new ProductPricingService(
                $app->make(BusinessCurrencyConfigRepository::class),
                $app->make(ProductRepository::class),
                $app->make(CurrencyRateService::class)
            );
        });
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
