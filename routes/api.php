<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SmsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/signup', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

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
    Route::post('/sms', [SmsController::class, 'store']);
    Route::put('/sms/{sms}', [SmsController::class, 'update']);
});
