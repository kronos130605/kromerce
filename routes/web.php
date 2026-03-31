<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes - Rutas Globales
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\StorefrontController;

// Welcome/Landing page
Route::get('/welcome', function () {
    return Inertia::render('Kromerce');
})->name('welcome');

// Marketplace - Storefront para clientes
Route::get('/', [StorefrontController::class, 'home'])->name('home');

// Rutas de perfil de usuario
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Include de archivos de rutas específicos
require __DIR__.'/auth.php';
require __DIR__.'/storefront.php';
require __DIR__.'/business.php';
require __DIR__.'/products.php';
require __DIR__.'/orders.php';
