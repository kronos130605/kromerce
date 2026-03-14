<?php

namespace App\Factories;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductTag;
use App\Models\Store;
use App\Models\StoreContact;
use App\Models\StoreCurrencyConfig;
use App\Models\StorePaymentMethod;
use App\Models\User;
use App\Repositories\BaseRepository;
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
use Spatie\Permission\Models\Role;

class RepositoryFactory
{
    /**
     * Create a new repository instance.
     */
    private static function createRepository(string $repositoryClass, object $model): BaseRepository
    {
        return match ($repositoryClass) {
            ProductRepository::class => new ProductRepository($model),
            ProductCategoryRepository::class => new ProductCategoryRepository($model),
            ProductTagRepository::class => new ProductTagRepository($model),
            StoreRepository::class => new StoreRepository($model),
            StoreBrandingRepository::class => new StoreBrandingRepository($model),
            StoreContactRepository::class => new StoreContactRepository($model),
            StorePaymentMethodRepository::class => new StorePaymentMethodRepository($model),
            StoreCurrencyConfigRepository::class => new StoreCurrencyConfigRepository($model),
            StoreStatisticsRepository::class => new StoreStatisticsRepository($model),
            StoreConfigRepository::class => new StoreConfigRepository($model),
            default => throw new \InvalidArgumentException("Repository {$repositoryClass} is not supported")
        };
    }

    /**
     * Get ProductRepository instance.
     */
    public static function productRepository(): ProductRepository
    {
        return new ProductRepository(new Product());
    }

    /**
     * Get ProductCategoryRepository instance.
     */
    public static function productCategoryRepository(): ProductCategoryRepository
    {
        return new ProductCategoryRepository(new ProductCategory());
    }

    /**
     * Get ProductTagRepository instance.
     */
    public static function productTagRepository(): ProductTagRepository
    {
        return new ProductTagRepository(new ProductTag());
    }

    // Store repositories
    public static function storeRepository(): StoreRepository
    {
        return new StoreRepository(new Store());
    }

    public static function storeBrandingRepository(): StoreBrandingRepository
    {
        return new StoreBrandingRepository(new Store());
    }

    public static function storeContactRepository(): StoreContactRepository
    {
        return new StoreContactRepository(new StoreContact());
    }

    public static function storePaymentMethodRepository(): StorePaymentMethodRepository
    {
        return new StorePaymentMethodRepository(new StorePaymentMethod());
    }

    public static function storeCurrencyConfigRepository(): StoreCurrencyConfigRepository
    {
        return new StoreCurrencyConfigRepository(new StoreCurrencyConfig());
    }

    public static function storeStatisticsRepository(): StoreStatisticsRepository
    {
        return new StoreStatisticsRepository(new Store());
    }

    public static function storeConfigRepository(): StoreConfigRepository
    {
        return new StoreConfigRepository(new Store());
    }

    // User repositories
    public static function userRoleRepository(): UserRoleRepository
    {
        return new UserRoleRepository();
    }

    public static function userTenantRepository(): UserStoreRepository
    {
        return new UserStoreRepository(new User());
    }

    public static function roleRepository(): RoleRepository
    {
        return new RoleRepository(new Role());
    }
}
