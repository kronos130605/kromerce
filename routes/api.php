<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserThemeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    
    // User Theme Management Routes
    Route::prefix('user')->group(function () {
        Route::get('/theme', [UserThemeController::class, 'index']);
        Route::post('/theme/toggle-dark', [UserThemeController::class, 'toggleDarkMode']);
        Route::put('/theme/preferences', [UserThemeController::class, 'updateThemePreferences']);
        Route::put('/theme/language', [UserThemeController::class, 'updateLanguage']);
        Route::post('/theme/reset', [UserThemeController::class, 'resetTheme']);
    });
    
});
