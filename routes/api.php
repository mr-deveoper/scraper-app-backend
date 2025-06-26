<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;

/**
 * API Routes
 * 
 * These routes handle all API requests for the application.
 * All routes are prefixed with 'api/' and use JSON responses.
 */

// Product routes
Route::prefix('products')->group(function () {
    // Get all products
    Route::get('/', [ProductController::class, 'index'])->name('api.products.index');
});
