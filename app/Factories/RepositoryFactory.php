<?php

namespace App\Factories;

use App\Repositories\Product\ProductCategoryRepository;
use App\Repositories\Product\ProductRepository;
use App\Repositories\Product\ProductSaleCurrencyRepository;
use App\Repositories\Product\ProductTagRepository;
use App\Repositories\Store\StoreActiveCurrencyRepository;
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

    public function productRepository(): ProductRepository
    {
        return app()->make(ProductRepository::class);
    }

    public function productCategoryRepository(): ProductCategoryRepository
    {
        return app()->make(ProductCategoryRepository::class);
    }

    public function productTagRepository(): ProductTagRepository
    {
        return app()->make(ProductTagRepository::class);
    }

    public function storeRepository(): StoreRepository
    {
        return app()->make(StoreRepository::class);
    }

    public function storeBrandingRepository(): StoreBrandingRepository
    {
        return app()->make(StoreBrandingRepository::class);
    }

    public function storeContactRepository(): StoreContactRepository
    {
        return app()->make(StoreContactRepository::class);
    }

    public function storePaymentMethodRepository(): StorePaymentMethodRepository
    {
        return app()->make(StorePaymentMethodRepository::class);
    }

    public function storeCurrencyConfigRepository(): StoreCurrencyConfigRepository
    {
        return app()->make(StoreCurrencyConfigRepository::class);
    }

    public function storeActiveCurrencyRepository(): StoreActiveCurrencyRepository
    {
        return app()->make(StoreActiveCurrencyRepository::class);
    }

    public function productSaleCurrencyRepository(): ProductSaleCurrencyRepository
    {
        return app()->make(ProductSaleCurrencyRepository::class);
    }

    public function storeStatisticsRepository(): StoreStatisticsRepository
    {
        return app()->make(StoreStatisticsRepository::class);
    }

    public function storeConfigRepository(): StoreConfigRepository
    {
        return app()->make(StoreConfigRepository::class);
    }

    public function userRoleRepository(): UserRoleRepository
    {
        return app()->make(UserRoleRepository::class);
    }

    public function roleRepository(): RoleRepository
    {
        return app()->make(RoleRepository::class);
    }

    public function userStoreRepository(): UserStoreRepository
    {
        return app()->make(UserStoreRepository::class);
    }

}
