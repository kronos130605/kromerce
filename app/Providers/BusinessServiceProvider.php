<?php

namespace App\Providers;

use App\Factories\RepositoryFactory;
use App\Models\BusinessCurrencyConfig;
use App\Models\CurrencyRateBusiness;
use App\Models\CurrencyRateGlobal;
use App\Models\CurrencyRateUpdate;
use App\Repositories\BusinessCurrencyConfigRepository;
use App\Repositories\Currency\CurrencyRateBusinessRepository;
use App\Repositories\Currency\CurrencyRateGlobalRepository;
use App\Repositories\Currency\CurrencyRateUpdateRepository;
use App\Repositories\Product\ProductCategoryRepository;
use App\Repositories\Product\ProductRepository;
use App\Repositories\Product\ProductTagRepository;
use App\Services\CurrencyRateService;
use App\Services\DashboardRoutingService;
use App\Services\DashboardService;
use App\Services\ProductPricingService;
use App\Services\ProductService;
use App\Services\RoleService;
use App\Services\StoreService;
use App\Services\StoreConfigService;
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
        // Legacy currency repositories (still needed for complex dependencies)
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

        // Product repositories (can use factory)
        $this->app->bind(ProductRepository::class, function ($app) {
            return RepositoryFactory::productRepository();
        });

        $this->app->bind(ProductCategoryRepository::class, function ($app) {
            return RepositoryFactory::productCategoryRepository();
        });

        $this->app->bind(ProductTagRepository::class, function ($app) {
            return RepositoryFactory::productTagRepository();
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

        // Register Dashboard Service with repositories
        $this->app->singleton(DashboardService::class, function ($app) {
            return new DashboardService(
                $app->make(ProductRepository::class),
                $app->make(ProductCategoryRepository::class),
                $app->make(ProductTagRepository::class),
                $app->make(BusinessCurrencyConfigRepository::class),
                $app->make(CurrencyRateService::class)
            );
        });

        // Register Store Service with repositories
        $this->app->singleton(StoreService::class, function ($app) {
            return new StoreService(
                RepositoryFactory::storeRepository(),
                RepositoryFactory::storeContactRepository(),
                RepositoryFactory::storePaymentMethodRepository(),
                RepositoryFactory::storeCurrencyConfigRepository(),
                RepositoryFactory::storeStatisticsRepository(),
                RepositoryFactory::userStoreRepository()
            );
        });

        // Register Dashboard Routing Service with repositories
        $this->app->singleton(DashboardRoutingService::class, function ($app) {
            return new DashboardRoutingService(
                $app->make(StoreService::class),
                $app->make(RoleService::class),
                RepositoryFactory::storeStatisticsRepository()
            );
        });

        // Register Store Config Service with repositories
        $this->app->singleton(StoreConfigService::class, function ($app) {
            return new StoreConfigService(
                RepositoryFactory::storeConfigRepository()
            );
        });

        // Register Role Service with repositories
        $this->app->singleton(RoleService::class, function ($app) {
            return new RoleService(
                RepositoryFactory::roleRepository()
            );
        });

        // Register Product Service with repositories
        $this->app->singleton(ProductService::class, function ($app) {
            return new ProductService(
                $app->make(\App\Factories\RepositoryFactory::class)
            );
        });
    }

    /**
     * Register additional model relationships.
     */
    private function registerModelRelationships(): void
    {
        // Store relationship with currency config
        \App\Models\Store::resolveRelationUsing('currencyConfig', function ($store) {
            return $store->hasOne(BusinessCurrencyConfig::class);
        });

        // User relationship with stores
        \App\Models\User::resolveRelationUsing('stores', function ($user) {
            return $user->belongsToMany(\App\Models\Store::class, 'store_users')
                ->withPivot(['is_active', 'joined_at'])
                ->withTimestamps();
        });
    }
}
