<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CurrencyController;

/*
|--------------------------------------------------------------------------
| Product Routes
|--------------------------------------------------------------------------
*/

// Product Management Routes
Route::middleware(['auth', 'verified'])->name('products.')->group(function () {
    
    // Test routes first
    Route::get('/', function () {
        return 'Products list - working!';
    })->name('index');
    
    Route::get('/create', function () {
        return 'Create product - working!';
    })->name('create');
    
    // Product CRUD (commented for now)
    // Route::get('/', [ProductController::class, 'index'])->name('index');
    // Route::get('/create', [ProductController::class, 'create'])->name('create');
    // Route::post('/', [ProductController::class, 'store'])->name('store');
    // Route::get('/{product}', [ProductController::class, 'show'])->name('show');
    // Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
    // Route::put('/{product}', [ProductController::class, 'update'])->name('update');
    // Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
    
    // Product Actions
    // Route::post('/{product}/duplicate', [ProductController::class, 'duplicate'])->name('duplicate');
    // Route::get('/{product}/prices', [ProductController::class, 'getPrices'])->name('prices');
    
    // Product Categories (to be implemented)
    // Route::prefix('categories')->name('categories.')->group(function () {
    //     Route::get('/', 'ProductCategoryController@index')->name('index');
    //     Route::post('/', 'ProductCategoryController@store')->name('store');
    //     Route::put('/{category}', 'ProductCategoryController@update')->name('update');
    //     Route::delete('/{category}', 'ProductCategoryController@destroy')->name('destroy');
    // });
    
    // Product Tags (to be implemented)
    // Route::prefix('tags')->name('tags.')->group(function () {
    //     Route::get('/', 'ProductTagController@index')->name('index');
    //     Route::post('/', 'ProductTagController@store')->name('store');
    //     Route::put('/{tag}', 'ProductTagController@update')->name('update');
    //     Route::delete('/{tag}', 'ProductTagController@destroy')->name('destroy');
    // });
    
    // Product Variants (to be implemented)
    // Route::prefix('variants')->name('variants.')->group(function () {
    //     Route::get('/{product}', 'ProductVariantController@index')->name('index');
    //     Route::post('/{product}', 'ProductVariantController@store')->name('store');
    //     Route::put('/{variant}', 'ProductVariantController@update')->name('update');
    //     Route::delete('/{variant}', 'ProductVariantController@destroy')->name('destroy');
    // });
    
    // Product Images (to be implemented)
    // Route::prefix('images')->name('images.')->group(function () {
    //     Route::post('/{product}', 'ProductImageController@store')->name('store');
    //     Route::put('/{image}', 'ProductImageController@update')->name('update');
    //     Route::delete('/{image}', 'ProductImageController@destroy')->name('destroy');
    //     Route::post('/{image}/set-primary', 'ProductImageController@setPrimary')->name('set_primary');
    // });
});

/*
|--------------------------------------------------------------------------
| Currency Routes
|--------------------------------------------------------------------------
*/

// Route::middleware(['auth', 'verified'])->prefix('currency')->name('currency.')->group(function () {
    
//     // Currency Configuration
//     Route::get('/', [CurrencyController::class, 'index'])->name('index');
//     Route::put('/config', [CurrencyController::class, 'updateConfig'])->name('config.update');
    
//     // Currency Rates
//     Route::get('/rates', [CurrencyController::class, 'getCurrentRates'])->name('rates.current');
//     Route::put('/rates/custom', [CurrencyController::class, 'updateCustomRates'])->name('rates.custom.update');
//     Route::get('/rates/history', [CurrencyController::class, 'getRateHistory'])->name('rates.history');
//     Route::post('/rates/reset', [CurrencyController::class, 'resetToGlobal'])->name('rates.reset');
    
//     // Rate Updates
//     Route::get('/updates/summary', [CurrencyController::class, 'getUpdateSummary'])->name('updates.summary');
// });

/*
|--------------------------------------------------------------------------
| API Routes for Products (for future use)
|--------------------------------------------------------------------------
*/

// Route::middleware(['auth:api', 'throttle:60,1'])->prefix('api/v1/products')->name('api.products.')->group(function () {
    
//     // Public Product API
//     Route::get('/', 'Api\ProductController@index')->name('index');
//     Route::get('/{product}', 'Api\ProductController@show')->name('show');
    
//     // Product Search
//     Route::get('/search', 'Api\ProductController@search')->name('search');
    
//     // Product Categories
//     Route::get('/categories', 'Api\ProductCategoryController@index')->name('categories');
    
//     // Product Tags
//     Route::get('/tags', 'Api\ProductTagController@index')->name('tags');
// });

/*
|--------------------------------------------------------------------------
| API Routes for Currency (for future use)
|--------------------------------------------------------------------------
*/

// // Route::middleware(['auth:api', 'throttle:60,1'])->prefix('api/v1/currency')->name('api.currency.')->group(function () {
    
//     // Public Currency API
//     Route::get('/rates', 'Api\CurrencyController@getCurrentRates')->name('rates');
//     Route::get('/rates/{from}/{to}', 'Api\CurrencyController@getRate')->name('rate');
//     Route::get('/rates/{from}/{to}/history', 'Api\CurrencyController@getRateHistory')->name('rate.history');
// });

/*
|--------------------------------------------------------------------------
| Webhook Routes (for future integrations)
|--------------------------------------------------------------------------
*/

// Route::middleware(['throttle:60,1'])->prefix('webhooks')->name('webhooks.')->group(function () {
    
//     // Currency Update Webhooks
//     Route::post('/currency/updated', 'Webhook\CurrencyController@rateUpdated')->name('currency.updated');
    
//     // Product Sync Webhooks
//     Route::post('/products/sync', 'Webhook\ProductController@sync')->name('products.sync');
// });
