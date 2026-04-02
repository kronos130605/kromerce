<?php

use App\Http\Controllers\Business\ProductController;
use App\Http\Controllers\Business\ProductQuestionController;
use App\Http\Controllers\Business\ProductReviewController;
use App\Http\Controllers\Business\ProductVariantController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Product Routes
|--------------------------------------------------------------------------
|
| Rutas para gestión de productos del negocio.
| Todas las rutas requieren autenticación y resolución de store.
|
*/

Route::middleware(['web', 'auth', 'verified', 'App\Http\Middleware\IdentifyStore'])
    ->prefix('products')
    ->name('products.')
    ->group(function () {
        // Product CRUD
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::get('/{product}', [ProductController::class, 'show'])->name('show');
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::match(['put', 'post'], '/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');

        // Product Images
        Route::post('/{product}/images', [ProductController::class, 'uploadImage'])->name('images.upload');
        Route::delete('/{product}/images/{image}', [ProductController::class, 'deleteImage'])->name('images.delete');

        // Bulk Actions
        Route::post('/bulk/status', [ProductController::class, 'bulkUpdateStatus'])->name('bulk.status');
        Route::delete('/bulk/delete', [ProductController::class, 'bulkDelete'])->name('bulk.delete');
        Route::post('/bulk/categories', [ProductController::class, 'bulkUpdateCategories'])->name('bulk.categories');
        Route::post('/bulk/price', [ProductController::class, 'bulkUpdatePrice'])->name('bulk.price');
        Route::get('/export', [ProductController::class, 'export'])->name('export');

        // Product Variants
        Route::get('/{product}/variants', [ProductVariantController::class, 'index'])->name('variants.index');
        Route::post('/{product}/variants', [ProductVariantController::class, 'store'])->name('variants.store');
        Route::put('/{product}/variants/{variant}', [ProductVariantController::class, 'update'])->name('variants.update');
        Route::delete('/{product}/variants/{variant}', [ProductVariantController::class, 'destroy'])->name('variants.destroy');
        Route::post('/{product}/variants/bulk', [ProductVariantController::class, 'bulkUpdate'])->name('variants.bulk');
        Route::patch('/{product}/variants/{variant}/stock', [ProductVariantController::class, 'updateStock'])->name('variants.stock');

        // Product Reviews
        Route::get('/{product}/reviews', [ProductReviewController::class, 'index'])->name('reviews.index');
        Route::post('/{product}/reviews', [ProductReviewController::class, 'store'])->name('reviews.store');
        Route::put('/{product}/reviews/{review}', [ProductReviewController::class, 'update'])->name('reviews.update');
        Route::delete('/{product}/reviews/{review}', [ProductReviewController::class, 'destroy'])->name('reviews.destroy');
        Route::post('/{product}/reviews/{review}/moderate', [ProductReviewController::class, 'moderate'])->name('reviews.moderate');
        Route::post('/{product}/reviews/{review}/vote', [ProductReviewController::class, 'vote'])->name('reviews.vote');
        Route::get('/{product}/reviews/stats', [ProductReviewController::class, 'stats'])->name('reviews.stats');

        // Product Q&A
        Route::get('/{product}/questions', [ProductQuestionController::class, 'index'])->name('questions.index');
        Route::post('/{product}/questions', [ProductQuestionController::class, 'store'])->name('questions.store');
        Route::put('/{product}/questions/{question}', [ProductQuestionController::class, 'update'])->name('questions.update');
        Route::delete('/{product}/questions/{question}', [ProductQuestionController::class, 'destroy'])->name('questions.destroy');
        Route::post('/{product}/questions/{question}/answers', [ProductQuestionController::class, 'storeAnswer'])->name('questions.answers.store');
        Route::post('/{product}/questions/{question}/answers/{answer}/vote', [ProductQuestionController::class, 'voteAnswer'])->name('questions.answers.vote');
    });
