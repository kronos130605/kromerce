<?php

namespace App\Providers;

use App\Factories\RepositoryFactory;
use App\Models\BusinessCurrencyConfig;
use App\Models\CurrencyRateBusiness;
use App\Models\CurrencyRateGlobal;
use App\Models\CurrencyRateUpdate;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductTag;
use App\Models\Store;
use App\Models\StoreContact;
use App\Models\StoreCurrencyConfig;
use App\Models\StorePaymentMethod;
use App\Models\User;
use App\Repositories\Store\BusinessCurrencyConfigRepository;
use App\Repositories\Currency\CurrencyRateBusinessRepository;
use App\Repositories\Currency\CurrencyRateGlobalRepository;
use App\Repositories\Currency\CurrencyRateUpdateRepository;
use App\Repositories\Product\ProductCategoryRepository;
use App\Repositories\Product\ProductRepository;
use App\Repositories\Product\ProductTagRepository;
use App\Repositories\Store\StoreBrandingRepository;
use App\Repositories\Store\StoreConfigRepository;
use App\Repositories\Store\StoreContactRepository;
use App\Repositories\Store\StoreCurrencyConfigRepository;
use App\Repositories\Store\StorePaymentMethodRepository;
use App\Repositories\Store\StoreRepository;
use App\Repositories\Store\StoreStatisticsRepository;
use App\Repositories\User\RoleRepository;
use App\Repositories\User\UserRoleRepository;
use App\Repositories\User\UserStoreRepository;
use App\Services\CurrencyRateService;
use App\Services\DashboardRoutingService;
use App\Services\DashboardService;
use App\Services\ProductPricingService;
use App\Services\ProductService;
use App\Services\RoleService;
use App\Services\StoreService;
use App\Services\StoreConfigService;
use App\Services\StoreUserService;
use App\Services\StorefrontService;
use App\Services\StorePageService;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Role;

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
        // Currency repositories
        $this->app->singleton(BusinessCurrencyConfigRepository::class,
            fn() => new BusinessCurrencyConfigRepository(new BusinessCurrencyConfig()));

        $this->app->singleton(CurrencyRateGlobalRepository::class,
            fn() => new CurrencyRateGlobalRepository(new CurrencyRateGlobal()));

        $this->app->singleton(CurrencyRateBusinessRepository::class,
            fn() => new CurrencyRateBusinessRepository(new CurrencyRateBusiness()));

        $this->app->singleton(CurrencyRateUpdateRepository::class,
            fn() => new CurrencyRateUpdateRepository(new CurrencyRateUpdate()));

        // Product repositories
        $this->app->singleton(ProductRepository::class,
            fn() => new ProductRepository(new Product()));

        $this->app->singleton(ProductCategoryRepository::class,
            fn() => new ProductCategoryRepository(new ProductCategory()));

        $this->app->singleton(ProductTagRepository::class,
            fn() => new ProductTagRepository(new ProductTag()));

        // Store repositories
        $this->app->singleton(StoreRepository::class,
            fn() => new StoreRepository(new Store()));

        $this->app->singleton(StoreBrandingRepository::class,
            fn() => new StoreBrandingRepository(new Store()));

        $this->app->singleton(StoreContactRepository::class,
            fn() => new StoreContactRepository(new StoreContact()));

        $this->app->singleton(StorePaymentMethodRepository::class,
            fn() => new StorePaymentMethodRepository(new StorePaymentMethod()));

        $this->app->singleton(StoreCurrencyConfigRepository::class,
            fn() => new StoreCurrencyConfigRepository(new StoreCurrencyConfig()));

        $this->app->singleton(StoreStatisticsRepository::class,
            fn() => new StoreStatisticsRepository(new Store()));

        $this->app->singleton(StoreConfigRepository::class,
            fn() => new StoreConfigRepository(new Store()));

        // User repositories
        $this->app->singleton(RoleRepository::class,
            fn() => new RoleRepository(new Role()));

        $this->app->singleton(UserRoleRepository::class,
            fn() => new UserRoleRepository());

        $this->app->singleton(UserStoreRepository::class,
            fn() => new UserStoreRepository(new User()));
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
                $app->make(StoreRepository::class),
                $app->make(StoreContactRepository::class),
                $app->make(StorePaymentMethodRepository::class),
                $app->make(StoreCurrencyConfigRepository::class),
                $app->make(StoreStatisticsRepository::class),
                $app->make(UserStoreRepository::class)
            );
        });

        // Register Store User Service
        $this->app->singleton(StoreUserService::class, function ($app) {
            return new StoreUserService(
                $app->make(UserStoreRepository::class),
                $app->make(StoreRepository::class)
            );
        });

        // Register Dashboard Routing Service
        $this->app->singleton(DashboardRoutingService::class, function ($app) {
            return new DashboardRoutingService(
                $app->make(StoreUserService::class),
                $app->make(RoleService::class),
                $app->make(StoreStatisticsRepository::class)
            );
        });

        // Register Store Config Service
        $this->app->singleton(StoreConfigService::class, function ($app) {
            return new StoreConfigService(
                $app->make(StoreConfigRepository::class)
            );
        });

        // Register Role Service
        $this->app->singleton(RoleService::class, function ($app) {
            return new RoleService(
                $app->make(RoleRepository::class)
            );
        });

        // Register Product Service
        $this->app->singleton(ProductService::class, function ($app) {
            return new ProductService(
                $app->make(ProductRepository::class),
                $app->make(ProductCategoryRepository::class)
            );
        });

        // Register Storefront Service
        $this->app->singleton(StorefrontService::class, function ($app) {
            return new StorefrontService(
                $app->make(ProductRepository::class),
                $app->make(ProductCategoryRepository::class),
                $app->make(StoreRepository::class)
            );
        });

        // Register Store Page Service
        $this->app->singleton(StorePageService::class, function ($app) {
            return new StorePageService(
                $app->make(StoreRepository::class),
                $app->make(ProductRepository::class)
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
