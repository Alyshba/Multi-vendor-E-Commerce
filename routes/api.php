<?php

use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\VendorApiController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'apiLogin']);

Route::middleware('jwt.auth')->group(function () {
    Route::get('/me', [AuthController::class, 'apiMe']);
    Route::post('/logout', [AuthController::class, 'apiLogout']);
    Route::apiResource('vendors', VendorApiController::class);
    Route::apiResource('products', ProductApiController::class);
    Route::patch('/products/{product}/approve', [ProductApiController::class, 'approve']);
    Route::patch('/products/{product}/reject', [ProductApiController::class, 'reject']);
});
