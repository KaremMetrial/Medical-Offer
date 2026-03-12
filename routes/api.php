<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Resources\UserResource;

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\HomeController;

Route::prefix('auth')->group(function () {
    // Standard rate limit for OTP sending: 6 times per minute per IP
    Route::post('/send-otp', [AuthController::class, 'sendOtp'])->middleware('throttle:6,1');
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/register', [AuthController::class, 'register']);
});

Route::get('/home', HomeController::class);
