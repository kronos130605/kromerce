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
     * Backward compatibility for legacy static calls.
     */
    public static function __callStatic(string $name, array $arguments)
    {
        $factory = app(self::class);

        if (!method_exists($factory, $name)) {
            throw new \BadMethodCallException("Method {$name} does not exist on " . self::class);
        }

        return $factory->{$name}(...$arguments);
    }

    /**
     * Get ProductRepository instance.
     */
    public function productRepository(): ProductRepository
    {
        return new ProductRepository(new Product());
    }

    /**
     * Get ProductCategoryRepository instance.
     */
    public function productCategoryRepository(): ProductCategoryRepository
    {
        return new ProductCategoryRepository(new ProductCategory());
    }

    /**
     * Get ProductTagRepository instance.
     */
    public function productTagRepository(): ProductTagRepository
    {
        return new ProductTagRepository(new ProductTag());
    }

    // Store repositories
    public function storeRepository(): StoreRepository
    {
        return new StoreRepository(new Store());
    }

    public function storeBrandingRepository(): StoreBrandingRepository
    {
        return new StoreBrandingRepository(new Store());
    }

    public function storeContactRepository(): StoreContactRepository
    {
        return new StoreContactRepository(new StoreContact());
    }

    public function storePaymentMethodRepository(): StorePaymentMethodRepository
    {
        return new StorePaymentMethodRepository(new StorePaymentMethod());
    }

    public function storeCurrencyConfigRepository(): StoreCurrencyConfigRepository
    {
        return new StoreCurrencyConfigRepository(new StoreCurrencyConfig());
    }

    public function storeStatisticsRepository(): StoreStatisticsRepository
    {
        return new StoreStatisticsRepository(new Store());
    }

    public function storeConfigRepository(): StoreConfigRepository
    {
        return new StoreConfigRepository(new Store());
    }

    // User repositories
    public function userRoleRepository(): UserRoleRepository
    {
        return new UserRoleRepository();
    }

    public function roleRepository(): RoleRepository
    {
        return new RoleRepository(new Role());
    }

    public function userStoreRepository(): UserStoreRepository
    {
        return new UserStoreRepository(new User());
    }

}
