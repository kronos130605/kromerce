<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\StorePageController;

/*
|--------------------------------------------------------------------------
| Storefront Routes
|--------------------------------------------------------------------------
|
| Rutas públicas para clientes del marketplace.
| No requieren autenticación.
|
*/

// Marketplace routes
Route::get('/products', [StorefrontController::class, 'products'])->name('products.index');
Route::get('/products/{product:slug}', [StorefrontController::class, 'productDetail'])->name('products.show');
Route::get('/category/{category:slug}', [StorefrontController::class, 'category'])->name('category.show');
Route::get('/search', [StorefrontController::class, 'search'])->name('search');

// Stores listing
Route::get('/stores', [StorefrontController::class, 'stores'])->name('stores.index');

// Individual store pages
Route::prefix('stores/{store:slug}')->name('store.')->group(function () {
    Route::get('/', [StorePageController::class, 'home'])->name('home');
    Route::get('/products', [StorePageController::class, 'products'])->name('products');
    Route::get('/about', [StorePageController::class, 'about'])->name('about');
});
