<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| Product Routes
|--------------------------------------------------------------------------
|
| Rutas para gestión de productos del negocio.
| Todas las rutas requieren autenticación y resolución de store.
|
*/

Route::middleware(['auth', 'verified', 'App\Http\Middleware\IdentifyStore'])
    ->prefix('products')
    ->name('products.')
    ->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::post('/{product}/images', [ProductController::class, 'uploadImage'])->name('images.upload');
        Route::delete('/{product}/images/{image}', [ProductController::class, 'deleteImage'])->name('images.delete');
        Route::get('/{product}', [ProductController::class, 'show'])->name('show');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
    });
