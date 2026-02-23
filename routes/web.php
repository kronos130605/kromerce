<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Landing page - Kromerce como página principal
Route::get('/', function () {
    return Inertia::render('Kromerce');
})->name('kromerce.app');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Dashboard específicos por rol
Route::get('/dashboard/customer', function () {
    return Inertia::render('DashboardCustomer');
})->middleware(['auth', 'verified'])->name('dashboard.customer');

Route::get('/dashboard/business', function () {
    return Inertia::render('DashboardBusiness');
})->middleware(['auth', 'verified'])->name('dashboard.business');

Route::get('/dashboard/admin', function () {
    return Inertia::render('DashboardAdmin');
})->middleware(['auth', 'verified'])->name('dashboard.admin');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
