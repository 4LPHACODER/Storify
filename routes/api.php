<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PhoneVerificationController;
use App\Http\Controllers\Api\SmsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/signup', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/phone/verify', [PhoneVerificationController::class, 'verify'])
    ->middleware('throttle:12,1');
Route::post('/phone/resend', [PhoneVerificationController::class, 'resend'])
    ->middleware('throttle:3,1');

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/test-auth', function () {
        return response()->json([
            'message' => 'Authenticated.',
            'status' => 'ok',
        ]);
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/sms', [SmsController::class, 'index']);
    Route::get('/sms/poll', [SmsController::class, 'poll']);
    Route::post('/sms', [SmsController::class, 'store']);
    Route::get('/sms/{sms}', [SmsController::class, 'show']);
    Route::put('/sms/{sms}', [SmsController::class, 'update']);
    Route::patch('/sms/{sms}', [SmsController::class, 'update']);
});
