<?php

use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/add-to-cart', [CartController::class, 'store'])->name('addToCart');
    Route::get('/carts', [CartController::class, 'index'])->name('carts');
    Route::delete('/carts/{cart}', [CartController::class, 'destroy'])->name('cartsDestory');
    Route::put('/cart/update/{product}', [CartController::class, 'update'])->name('cartsUpdate');
    Route::get('/carts/checkout/', [CartController::class, 'showAll'])->name('carts.show');
});
