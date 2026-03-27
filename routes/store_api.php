<?php

use App\Http\Controllers\Api\StoreController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Store API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for store management.
| These routes are loaded by the RouteServiceProvider within a group which
| contains the "api" middleware group. Build something great!
|
*/

// Store management routes
Route::prefix('stores')->group(function () {
    Route::get('/', [StoreController::class, 'index'])->name('stores.index');
    Route::post('/', [StoreController::class, 'store'])->name('stores.store');
    Route::get('/{store}', [StoreController::class, 'show'])->name('stores.show');
    Route::put('/{store}', [StoreController::class, 'update'])->name('stores.update');
    Route::patch('/{store}', [StoreController::class, 'update'])->name('stores.patch');
    Route::delete('/{store}', [StoreController::class, 'destroy'])->name('stores.destroy');
    
    // Store management actions
    Route::post('/{store}/toggle-status', [StoreController::class, 'toggleStatus'])->name('stores.toggle-status');
    Route::get('/{store}/statistics', [StoreController::class, 'statistics'])->name('stores.statistics');
    Route::post('/{store}/verify', [StoreController::class, 'verify'])->name('stores.verify');
});

// Store contact routes
Route::prefix('stores/{store}/contacts')->group(function () {
    Route::get('/', [StoreContactController::class, 'index'])->name('stores.contacts.index');
    Route::post('/', [StoreContactController::class, 'store'])->name('stores.contacts.store');
    Route::get('/{contact}', [StoreContactController::class, 'show'])->name('stores.contacts.show');
    Route::put('/{contact}', [StoreContactController::class, 'update'])->name('stores.contacts.update');
    Route::delete('/{contact}', [StoreContactController::class, 'destroy'])->name('stores.contacts.destroy');
});

// Store payment method routes
Route::prefix('stores/{store}/payment-methods')->group(function () {
    Route::get('/', [StorePaymentMethodController::class, 'index'])->name('stores.payment-methods.index');
    Route::post('/', [StorePaymentMethodController::class, 'store'])->name('stores.payment-methods.store');
    Route::get('/{paymentMethod}', [StorePaymentMethodController::class, 'show'])->name('stores.payment-methods.show');
    Route::put('/{paymentMethod}', [StorePaymentMethodController::class, 'update'])->name('stores.payment-methods.update');
    Route::delete('/{paymentMethod}', [StorePaymentMethodController::class, 'destroy'])->name('stores.payment-methods.destroy');
    Route::patch('/{paymentMethod}/toggle', [StorePaymentMethodController::class, 'toggle'])->name('stores.payment-methods.toggle');
});
