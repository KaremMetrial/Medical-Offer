<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Resources\UserResource;
use App\Http\Controllers\Api\{
    Auth\AuthController,
    HomeController,
    CategoryController,
    ProviderController,
    OfferController,
    CountryController,
    CityController,
    FavoriteController,
    SectionController,
};
Route::prefix('v1')->group(function(){
    
    Route::prefix('auth')->group(function () {
        // Standard rate limit for OTP sending: 6 times per minute per IP
        Route::post('/send-otp', [AuthController::class, 'sendOtp'])->middleware('throttle:6,1');
        Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
        Route::post('/register', [AuthController::class, 'register']);
    });

    Route::get('/home', HomeController::class);

    Route::prefix('categories')->controller(CategoryController::class)->name('categories.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/by-section/{sectionId}', 'getParentActiveCategoriesBySectionId')->name('bySection');
        Route::get('/{id}', 'show')->name('show');
    });

    Route::get('/sections', [SectionController::class, 'index']);
    
    Route::get('/providers', [ProviderController::class, 'index']);
    Route::get('/providers/{id}', [ProviderController::class, 'show']);
    Route::get('/offers', [OfferController::class, 'index']);
    Route::get('/offers/{id}', [OfferController::class, 'show']);
    Route::get('/countries', [CountryController::class, 'index']);
    Route::get('/cities', [CityController::class, 'index']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/favorites', [FavoriteController::class, 'index']);
        Route::post('/favorites/toggle', [FavoriteController::class, 'toggle']);
    });
});
