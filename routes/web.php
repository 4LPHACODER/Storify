<?php

use App\Http\Controllers\Auth\PhoneVerificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SocialiteLoginController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('auth/google/redirect', [SocialiteLoginController::class, 'redirectToGoogle'])->name('socialite.google.redirect');
    Route::get('auth/google/callback', [SocialiteLoginController::class, 'handleGoogleCallback'])->name('socialite.google.callback');
});

Route::middleware('auth')->group(function (): void {
    Route::get('register/verify-phone', [PhoneVerificationController::class, 'show'])
        ->name('register.phone-verification.show');
    Route::post('register/verify-phone', [PhoneVerificationController::class, 'store'])
        ->middleware('throttle:12,1')
        ->name('register.phone-verification.store');
    Route::post('register/verify-phone/resend', [PhoneVerificationController::class, 'resend'])
        ->middleware('throttle:3,1')
        ->name('register.phone-verification.resend');
});

Route::middleware(['auth', 'verified', 'phone.verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
});

require __DIR__.'/admin.php';
require __DIR__.'/customer.php';
require __DIR__.'/settings.php';
