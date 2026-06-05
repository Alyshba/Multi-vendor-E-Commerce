<?php

use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\VendorApiController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'apiLogin']);

Route::middleware(['jwt.auth', 'role:admin'])->prefix('admin')->name('api.admin.')->group(function () {
    Route::get('/me', [AuthController::class, 'apiMe']);
    Route::post('/logout', [AuthController::class, 'apiLogout']);
    Route::apiResource('vendors', VendorApiController::class);
    Route::apiResource('products', ProductApiController::class);
    Route::patch('/products/{product}/approve', [ProductApiController::class, 'approve']);
    Route::patch('/products/{product}/reject', [ProductApiController::class, 'reject']);
    Route::get('/charts', [ProductApiController::class, 'chartData']);
});
Route::get('products', [ProductApiController::class, 'index']);

// Customer v1 API routes
use App\Http\Controllers\Api\Customer\AuthController as CustomerApiAuthController;
use App\Http\Controllers\Api\Customer\CartController as CustomerApiCartController;
use App\Http\Controllers\Api\Customer\CategoryController as CustomerApiCategoryController;
use App\Http\Controllers\Api\Customer\OrderController as CustomerApiOrderController;
use App\Http\Controllers\Api\Customer\ProductController as CustomerApiProductController;

Route::prefix('v1')->group(function () {
    // Auth
    Route::post('/auth/register', [CustomerApiAuthController::class, 'register']);
    Route::post('/auth/login', [CustomerApiAuthController::class, 'login']);

    // Public routes
    Route::get('/products', [CustomerApiProductController::class, 'index']);
    Route::get('/products/featured', [CustomerApiProductController::class, 'featured']);
    Route::get('/products/{slug}', [CustomerApiProductController::class, 'show']);
    Route::get('/categories', [CustomerApiCategoryController::class, 'index']);
    Route::get('/categories/{slug}', [CustomerApiCategoryController::class, 'show']);

    // Authenticated routes
    Route::middleware(['auth:api', 'role:customer'])->group(function () {
        Route::get('/auth/me', [CustomerApiAuthController::class, 'me']);
        Route::post('/auth/logout', [CustomerApiAuthController::class, 'logout']);
        Route::post('/auth/refresh', [CustomerApiAuthController::class, 'refresh']);

        // Cart
        Route::get('/cart', [CustomerApiCartController::class, 'index']);
        Route::post('/cart/add', [CustomerApiCartController::class, 'add']);
        Route::patch('/cart/{cart}', [CustomerApiCartController::class, 'update']);
        Route::delete('/cart/{cart}', [CustomerApiCartController::class, 'destroy']);
        Route::delete('/cart', [CustomerApiCartController::class, 'clear']);

        // Orders
        Route::get('/orders', [CustomerApiOrderController::class, 'index']);
        Route::post('/orders', [CustomerApiOrderController::class, 'store']);
        Route::get('/orders/{order}', [CustomerApiOrderController::class, 'show']);
        Route::patch('/orders/{order}/cancel', [CustomerApiOrderController::class, 'cancel']);
    });
});
// Vendor API routes
Route::prefix('vendor')->group(function () {
    Route::post('/register', [\App\Http\Controllers\Api\Vendor\AuthController::class, 'register']);
    Route::post('/login',    [\App\Http\Controllers\Api\Vendor\AuthController::class, 'login']);
    Route::get('/categories', [\App\Http\Controllers\Api\Vendor\CategoryController::class, 'index']);
    Route::get('/categories/{id}', [\App\Http\Controllers\Api\Vendor\CategoryController::class, 'show']);

    Route::middleware(['jwt.auth', 'role:vendor'])->group(function () {
        Route::post('/logout',  [\App\Http\Controllers\Api\Vendor\AuthController::class, 'logout']);
        Route::post('/refresh', [\App\Http\Controllers\Api\Vendor\AuthController::class, 'refresh']);
        Route::get('/me',       [\App\Http\Controllers\Api\Vendor\AuthController::class, 'me']);

        Route::get('/products',         [\App\Http\Controllers\Api\Vendor\ProductApiController::class, 'index']);
        Route::get('/products/{id}',    [\App\Http\Controllers\Api\Vendor\ProductApiController::class, 'show']);
        Route::post('/products',        [\App\Http\Controllers\Api\Vendor\ProductApiController::class, 'store']);
        Route::post('/products/{id}',   [\App\Http\Controllers\Api\Vendor\ProductApiController::class, 'update']);
        Route::put('/products/{id}',    [\App\Http\Controllers\Api\Vendor\ProductApiController::class, 'update']);
        Route::delete('/products/{id}', [\App\Http\Controllers\Api\Vendor\ProductApiController::class, 'destroy']);

        Route::get('/orders',                      [\App\Http\Controllers\Api\Vendor\OrderApiController::class, 'index']);
        Route::get('/orders/{id}',                 [\App\Http\Controllers\Api\Vendor\OrderApiController::class, 'show']);
        Route::put('/orders/{id}/status',          [\App\Http\Controllers\Api\Vendor\OrderApiController::class, 'updateStatus']);

        Route::post('/categories', [\App\Http\Controllers\Api\Vendor\CategoryController::class, 'store']);
    });
});
