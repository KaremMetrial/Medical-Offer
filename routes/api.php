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
    StoryController,
    NationalityController,
    EnumController,
    GovernorateController,
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

    Route::prefix('stories')->controller(StoryController::class)->name('stories.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/by-provider/{providerId}', 'getStoriesByProviderId')->name('byProvider');
        Route::get('/{id}', 'show')->name('show');
        Route::post('/{id}/view', 'recordView')->name('view');
    });

    Route::prefix('nationalities')->controller(NationalityController::class)->name('nationalities.')->group(function () {
        Route::get('/', 'index')->name('index');
    });

    Route::controller(EnumController::class)->group(function () {
        Route::get('/genders', 'genders')->name('genders');
        Route::get('/ratings', 'ratings')->name('ratings');
        Route::get('/discounts', 'discounts')->name('discounts');
        Route::get('/sections', 'sections')->name('sections');
    });

    Route::prefix('providers')->controller(ProviderController::class)->name('providers.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'show')->name('show');
        Route::get('/by-category/{categoryId}', 'getProvidersByCategory')->name('byCategory');
    });

    Route::get('/governorates', [GovernorateController::class, 'index']);    
    
    Route::get('/offers', [OfferController::class, 'index']);
    Route::get('/offers/{id}', [OfferController::class, 'show']);
    Route::get('/countries', [CountryController::class, 'index']);
    Route::get('/cities', [CityController::class, 'index']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/favorites', [FavoriteController::class, 'index']);
        Route::post('/favorites/toggle', [FavoriteController::class, 'toggle']);
    });
});
