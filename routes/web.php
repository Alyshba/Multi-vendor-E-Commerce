<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VendorController;

// Customer Web Controllers
use App\Http\Controllers\Customer\AuthController as CustomerAuthController;
use App\Http\Controllers\Customer\CartController as CustomerCartController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Customer\ProductController as CustomerProductController;
use App\Http\Controllers\Customer\ProfileController as CustomerProfileController;

use Illuminate\Support\Facades\Route;

// Public storefront (no auth required)
Route::name('customer.')->group(function () {
    Route::get('/', function () {
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->role === 'vendor') {
                return redirect()->route('vendor.products.index');
            } elseif ($user->role === 'customer') {
                return app(CustomerProductController::class)->index(request());
            }
            return redirect()->route('dashboard');
        }
        return redirect()->route('login');
    })->name('products');
    Route::get('/product/{slug}', [CustomerProductController::class, 'show'])->name('product.show');
});

// Customer portal
Route::prefix('customer')->name('customer.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [CustomerAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [CustomerAuthController::class, 'login'])->name('login');
        Route::get('/register', [CustomerAuthController::class, 'showRegisterForm'])->name('register');
        Route::post('/register', [CustomerAuthController::class, 'register'])->name('register');
    });

    Route::middleware(['auth', 'role:customer'])->group(function () {
        Route::post('/logout', [CustomerAuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');

        Route::get('/cart', [CustomerCartController::class, 'index'])->name('cart');
        Route::post('/cart/add', [CustomerCartController::class, 'add'])->name('cart.add');
        Route::patch('/cart/{cart}', [CustomerCartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/{cart}', [CustomerCartController::class, 'destroy'])->name('cart.remove');

        Route::get('/checkout', [CustomerOrderController::class, 'checkout'])->name('checkout');
        Route::post('/orders', [CustomerOrderController::class, 'placeOrder'])->name('order.place');

        Route::get('/orders', [CustomerOrderController::class, 'index'])->name('orders');
        Route::get('/orders/{order}', [CustomerOrderController::class, 'show'])->name('order.show');
        Route::patch('/orders/{order}/cancel', [CustomerOrderController::class, 'cancel'])->name('order.cancel');

        Route::get('/profile', [CustomerProfileController::class, 'index'])->name('profile');
        Route::patch('/profile', [CustomerProfileController::class, 'update'])->name('profile.update');
        Route::patch('/profile/password', [CustomerProfileController::class, 'updatePassword'])->name('profile.password');
    });
});
// Admin portal
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('vendors', VendorController::class);
    Route::resource('products', ProductController::class);
    Route::patch('/products/{product}/approve', [ProductController::class, 'approve'])->name('products.approve');
    Route::patch('/products/{product}/reject', [ProductController::class, 'reject'])->name('products.reject');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/download', [ReportController::class, 'download'])->name('reports.download');
});

// Vendor portal
Route::prefix('vendor')->name('vendor.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [\App\Http\Controllers\Vendor\AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [\App\Http\Controllers\Vendor\AuthController::class, 'login'])->name('login.submit');
    });

    Route::middleware(['auth', 'role:vendor'])->group(function () {
        Route::post('/logout', [\App\Http\Controllers\Vendor\AuthController::class, 'logout'])->name('logout');
        Route::get('/products/report/pdf', [\App\Http\Controllers\Vendor\ProductController::class, 'pdfReport'])->name('products.pdf');
        Route::get('/products/{id}/delete', [\App\Http\Controllers\Vendor\ProductController::class, 'confirmDelete'])->name('products.delete');
        Route::post('/products/{id}/delete', [\App\Http\Controllers\Vendor\ProductController::class, 'destroy'])->name('products.destroy_confirm');
        Route::resource('products', \App\Http\Controllers\Vendor\ProductController::class);
    });
});
