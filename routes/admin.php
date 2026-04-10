<?php

use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    Route::resource('admin/products', ProductController::class)
        ->names('admin.products');

    Route::resource('admin/orders', OrderController::class)
        ->only(['index', 'show', 'update'])
        ->names('admin.orders');

    Route::get('admin/analytics', AnalyticsController::class)
        ->name('admin.analytics.index');
});
