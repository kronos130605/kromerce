<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Business\SettingsController;
use App\Http\Controllers\Business\CurrencySourceController;
use App\Http\Controllers\Business\StoreCurrencyController;

/*
|--------------------------------------------------------------------------
| Business Routes
|--------------------------------------------------------------------------
|
| Rutas para el dashboard y gestión de negocio.
| Todas las rutas requieren autenticación y resolución de store.
|
*/

// Rutas prefijadas bajo /business
Route::middleware(['auth', 'verified', 'App\Http\Middleware\IdentifyStore'])
    ->prefix('business')
    ->name('business.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
        Route::put('/settings/currency-source', [SettingsController::class, 'updateCurrencySource'])->name('settings.currency-source');
        Route::post('/currency-sources/test', [CurrencySourceController::class, 'test'])->name('currency-sources.test');

        // Active currencies management
        Route::prefix('currencies')->name('currencies.')->group(function () {
            Route::get('/', [StoreCurrencyController::class, 'index'])->name('index');
            Route::post('/activate', [StoreCurrencyController::class, 'activate'])->name('activate');
            Route::post('/deactivate', [StoreCurrencyController::class, 'deactivate'])->name('deactivate');
            Route::post('/deactivate-migrate', [StoreCurrencyController::class, 'deactivateWithMigration'])->name('deactivate.migrate');
            Route::post('/reorder', [StoreCurrencyController::class, 'reorder'])->name('reorder');
            Route::get('/products/{productId}/sale-currencies', [StoreCurrencyController::class, 'productSaleCurrencies'])->name('products.sale-currencies');
            Route::put('/products/{productId}/sale-currencies', [StoreCurrencyController::class, 'updateProductSaleCurrencies'])->name('products.sale-currencies.update');
        });
    });

// Rutas legacy sin prefijo (usadas por el sidebar)
Route::middleware(['auth', 'verified', 'App\Http\Middleware\IdentifyStore'])
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
        Route::put('/settings/currency-source', [SettingsController::class, 'updateCurrencySource'])->name('settings.currency-source');
    });
