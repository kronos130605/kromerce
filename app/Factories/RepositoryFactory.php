<?php

namespace App\Factories;

use App\Repositories\BaseRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ProductCategoryRepository;
use App\Repositories\ProductTagRepository;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductTag;

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
}
