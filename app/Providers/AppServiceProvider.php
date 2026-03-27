<?php

namespace App\Providers;

use App\Factories\RepositoryFactory;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(RepositoryFactory::class, fn() => new RepositoryFactory());
    }

    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
        Vite::useBuildDirectory('build');
    }
}
