<?php

use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\OrderController;
use App\Http\Controllers\Customer\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'customer'])->group(function () {
    Route::get('products', [ProductController::class, 'index'])
        ->name('customer.products.index');
    Route::get('products/{product}', [ProductController::class, 'show'])
        ->name('customer.products.show');

    Route::get('cart', [CartController::class, 'index'])
        ->name('customer.cart.index');
    Route::post('cart/items', [CartController::class, 'store'])
        ->name('customer.cart.store');
    Route::patch('cart/items/{cartItem}', [CartController::class, 'update'])
        ->name('customer.cart.update');
    Route::delete('cart/items/{cartItem}', [CartController::class, 'destroy'])
        ->name('customer.cart.destroy');
    Route::post('cart/buy-now', [CartController::class, 'buyNow'])
        ->name('customer.cart.buy-now');

    Route::get('checkout', [CheckoutController::class, 'index'])
        ->name('customer.checkout.index');
    Route::post('checkout', [CheckoutController::class, 'store'])
        ->name('customer.checkout.store');

    Route::get('orders', [OrderController::class, 'index'])
        ->name('customer.orders.index');
    Route::get('orders/{order}', [OrderController::class, 'show'])
        ->name('customer.orders.show');
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])
        ->name('customer.orders.update-status');
});
