<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Business Routes
|--------------------------------------------------------------------------
|
| Rutas para el dashboard y gestión de negocio.
| Todas las rutas requieren autenticación y resolución de store.
|
*/

// Dashboard principal de negocio
Route::middleware(['auth', 'verified', 'App\Http\Middleware\IdentifyStore'])
    ->prefix('business')
    ->name('business.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });

// También mantener la ruta legacy /dashboard para negocio
Route::middleware(['auth', 'verified', 'App\Http\Middleware\IdentifyStore'])
    ->get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');
