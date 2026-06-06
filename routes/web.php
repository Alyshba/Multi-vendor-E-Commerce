<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('products.index');
});

Route::get('/home', function () {
    return redirect()->route('products.index');
});


Route::get('/products/report/pdf', [ProductController::class, 'pdfReport'])
    ->name('products.pdf');
// Show delete confirmation page
Route::get('/products/{id}/delete', [ProductController::class, 'confirmDelete'])
    ->name('products.delete');

// Perform actual deletion
Route::post('/products/{id}/delete', [ProductController::class, 'destroy'])
    ->name('products.destroy_confirm');

Route::resource('products', ProductController::class);