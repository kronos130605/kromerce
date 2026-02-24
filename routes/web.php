<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Health check con log file
Route::get('/health', function () {
    $logPath = storage_path('logs/laravel.log');
    
    try {
        $exists = File::exists($logPath);
        $size = $exists ? File::size($logPath) : 0;
        $modified = $exists ? File::lastModified($logPath) : null;
        
        // Leer todo el contenido del log si existe
        $logContent = '';
        if ($exists && $size > 0) {
            $logContent = File::get($logPath);
        }
        
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toISOString(),
            'service' => 'kromerce',
            'log_file' => [
                'exists' => $exists,
                'path' => $logPath,
                'size_bytes' => $size,
                'last_modified' => $modified ? date('Y-m-d H:i:s', $modified) : null,
                'content' => $logContent,
                'lines_count' => $exists ? count(File::lines($logPath)) : 0
            ]
        ], 200);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'timestamp' => now()->toISOString(),
            'service' => 'kromerce',
            'error' => 'Failed to read log file: ' . $e->getMessage()
        ], 500);
    }
});

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
