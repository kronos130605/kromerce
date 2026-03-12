<?php

namespace App\Providers;

use App\Factories\RepositoryFactory;
use App\Repositories\ProductCategoryRepository;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use App\Services\ProductService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register RepositoryFactory as singleton
        $this->app->singleton(RepositoryFactory::class, function ($app) {
            return new RepositoryFactory();
        });

        // Register repositories using factory
        $this->app->singleton(ProductRepository::class, function ($app) {
            return $app->make(RepositoryFactory::class)->productRepository();
        });

        $this->app->singleton(ProductCategoryRepository::class, function ($app) {
            return $app->make(RepositoryFactory::class)->productCategoryRepository();
        });

        // Register ProductService using factory
        $this->app->singleton(ProductService::class, function ($app) {
            return new ProductService(
                $app->make(RepositoryFactory::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
        Vite::useBuildDirectory('build');
    }
}
