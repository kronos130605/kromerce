<?php

use App\Http\Controllers\Storefront\StorefrontController;
use App\Http\Controllers\Storefront\StorePageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Storefront Routes - Marketplace Público
|--------------------------------------------------------------------------
|
| Rutas públicas para clientes del marketplace.
| Prefijo /marketplace para evitar conflictos con rutas de administración.
| No requieren autenticación.
|
*/

Route::prefix('marketplace')->name('marketplace.')->group(function () {
    // Home del marketplace
    Route::get('/', [StorefrontController::class, 'home'])->name('home');

    // Productos
    Route::get('/products', [StorefrontController::class, 'products'])->name('products.index');
    Route::get('/products/{productId}', [StorefrontController::class, 'productDetail'])->name('products.show');

    // Categorías
    Route::get('/category/{category:slug}', [StorefrontController::class, 'category'])->name('category.show');

    // Búsqueda
    Route::get('/search', [StorefrontController::class, 'search'])->name('search');

    // Tiendas
    Route::get('/stores', [StorefrontController::class, 'stores'])->name('stores.index');
    Route::prefix('/stores/{store:slug}')->name('store.')->group(function () {
        Route::get('/', [StorePageController::class, 'home'])->name('home');
        Route::get('/products', [StorePageController::class, 'products'])->name('products');
        Route::get('/about', [StorePageController::class, 'about'])->name('about');
    });
});
