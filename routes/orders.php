<?php

use App\Http\Controllers\Business\OrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Order Routes
|--------------------------------------------------------------------------
|
| Rutas para gestión de órdenes/pedidos del negocio.
| Todas las rutas requieren autenticación y resolución de store.
|
*/

Route::middleware(['auth', 'verified', 'App\Http\Middleware\IdentifyStore'])
    ->prefix('orders')
    ->name('orders.')
    ->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::put('/{order}/status', [OrderController::class, 'updateStatus'])->name('status.update');
        Route::put('/{order}/payment', [OrderController::class, 'updatePaymentStatus'])->name('payment.update');
        Route::delete('/{order}', [OrderController::class, 'cancel'])->name('cancel');
    });
