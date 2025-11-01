<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'can:view-admin-panel'])->group(function() {
    Route::resource('products', ProductController::class);
});

Route::middleware(['auth', 'verified'])->group(function(){
    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/{product}', [ProductController::class, 'show'])
         ->name('products.show');
});