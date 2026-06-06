<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\ProductApiController;
use Illuminate\Support\Facades\Route;

// ─── Test route ───────────────────────────────────────────────────────────────

Route::get('/test', function () {
    return response()->json([
        'status'  => 'success',
        'message' => 'Vendor Panel API is working',
        'version' => '1.0',
    ]);
});

// ─── Public Auth Routes ───────────────────────────────────────────────────────

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// ─── Public Category Route (browsable without login) ─────────────────────────

Route::get('/categories',     [CategoryController::class, 'index']);
Route::get('/categories/{id}',[CategoryController::class, 'show']);

// ─── Public Order Create (customers can place orders) ────────────────────────

Route::post('/orders', [OrderApiController::class, 'store']);

// ─── Protected Routes (require JWT token) ────────────────────────────────────

Route::middleware('jwt.auth')->group(function () {

    // Auth
    Route::post('/logout',  [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/me',       [AuthController::class, 'me']);

    // Products (vendor's own)
    Route::get('/products',         [ProductApiController::class, 'index']);
    Route::get('/products/{id}',    [ProductApiController::class, 'show']);
    Route::post('/products',        [ProductApiController::class, 'store']);
    Route::post('/products/{id}',   [ProductApiController::class, 'update']);   // POST with _method=PUT also works
    Route::put('/products/{id}',    [ProductApiController::class, 'update']);
    Route::delete('/products/{id}', [ProductApiController::class, 'destroy']);

    // Orders
    Route::get('/orders',                      [OrderApiController::class, 'index']);
    Route::get('/orders/{id}',                 [OrderApiController::class, 'show']);
    Route::put('/orders/{id}/status',          [OrderApiController::class, 'updateStatus']);

    // Categories (create protected)
    Route::post('/categories', [CategoryController::class, 'store']);
});
