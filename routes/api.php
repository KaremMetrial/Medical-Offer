<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Resources\UserResource;
use App\Http\Controllers\MyFatoorahController;

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
    ReviewController,
    MemberPlanController,
    PageController,
    SubscriptionController,
    CardRequestController,
    WalletController,
    WithdrawalController,
    NotificationController,
    VisitController,
    ComplaintController,
    Provider\HomeController as ProviderHomeController
};

Route::prefix('v1')->group(function () {
    Route::get('/faqs', [PageController::class, 'faqs']);
    Route::get('/terms', [PageController::class, 'terms']);
    Route::get('/contact-us', [PageController::class, 'contactUs']);


    Route::prefix('member-plans')->controller(MemberPlanController::class)->name('member-plans.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'show')->name('show');
    });

    Route::prefix('auth')->controller(AuthController::class)->name('auth.')->group(function () {
        // Standard rate limit for OTP sending: 6 times per minute per IP
        Route::post('/send-otp', 'sendOtp')->middleware('throttle:6,1')->name('sendOtp');
        Route::post('/verify-otp', 'verifyOtp')->name('verifyOtp');
        Route::post('/register', 'register')->name('register');
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
        Route::get('/relationship-types', 'relationshipTypes')->name('relationshipTypes');
        Route::get('/wallet-transaction-types', 'walletTransactionTypes')->name('walletTransactionTypes');
        Route::get('/notification-types', 'notificationTypes')->name('notificationTypes');
    });

    Route::prefix('providers')->controller(ProviderController::class)->name('providers.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'show')->name('show');
        Route::get('/by-category/{categoryId}', 'getProvidersByCategory')->name('byCategory');
    });

    Route::prefix('reviews')->controller(ReviewController::class)->name('reviews.')->group(function () {
        Route::get('/by-provider/{providerId}', 'getReviewsByProviderId')->name('byProvider');
    });

    Route::get('/governorates', [GovernorateController::class, 'index']);
    Route::get('/countries', [CountryController::class, 'index']);
    Route::get('/cities', [CityController::class, 'index']);




    Route::get('/offers', [OfferController::class, 'index']);
    Route::get('/offers/{id}', [OfferController::class, 'show']);
    Route::post('/complaints', [ComplaintController::class, 'store'])->name('complaints.store');


    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('subscription')->controller(SubscriptionController::class)->name('subscription.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/upgrate-plans', 'upgratePlans')->name('upgrate-plans');
            Route::post('/add-companion', 'addCompanion')->name('add-companion');
            Route::post('/', 'subscribe')->name('subscribe');
            Route::get('/invoices', 'invoices')->name('invoices');
            Route::get('/companions/{id}', 'showCompanion')->name('companions.show');
            Route::post('/companions/{id}/status', 'companionActionStatus')->name('companions.status');
        });

        Route::prefix('favorites')->controller(FavoriteController::class)->name('favorites.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/toggle', 'toggle')->name('toggle');
        });

        Route::prefix('reviews')->controller(ReviewController::class)->name('reviews.')->group(function () {
            Route::post('/', 'store')->name('store');
        });

        Route::prefix('profile')->controller(AuthController::class)->name('profile.')->group(function () {
            Route::get('/', 'profile')->name('index');
            Route::post('/', 'updateProfile')->name('update');
            Route::post('/fcm-token', 'updateFcmToken')->name('updateFcmToken');
        });

        Route::prefix('card-request')->controller(CardRequestController::class)->group(function () {
            Route::get('/init', 'init');
            Route::post('/', 'store');
            Route::get('/status', 'status');
        });

        Route::prefix('wallet')->controller(WalletController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('/transactions', 'transactions');
            Route::post('/topup', 'topup');
        });


        Route::prefix('withdrawals')->controller(WithdrawalController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::get('/{id}', 'show');
        });

        Route::prefix('notifications')->controller(NotificationController::class)->group(function () {
            Route::get('/', 'index');
            Route::patch('/{id}/read', 'markAsRead');
            Route::post('/read-all', 'markAllAsRead');
            Route::delete('/{id}', 'destroy');
        });

        Route::prefix('visits')->controller(VisitController::class)->name('visits.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{id}', 'show')->name('show');
        });

        Route::prefix('provider')->controller(ProviderHomeController::class)->middleware('role:provider')->name('provider.')->group(function () {
            Route::get('/home', 'index')->name('index');
            Route::get('/user-card/{card_code}', 'getUserByCard')->name('getUserByCard');
            Route::get('/offers', 'offers')->name('offers');

            Route::prefix('visits')->name('visits.')->group(function () {
                Route::get('/', 'visits')->name('index');
                Route::post('/', 'registerVisit')->name('registerVisit');
                Route::get('/{id}', 'showVisit')->name('showVisit');
            });
        });
    });

    Route::prefix('myfatoorah')->group(function () {
        Route::get('index', [\App\Http\Controllers\MyFatoorahController::class, 'index'])->name('myfatoorah.index');
        Route::get('gateway', [\App\Http\Controllers\MyFatoorahController::class, 'gateway'])->name('myfatoorah.gateway');
        Route::get('process', [\App\Http\Controllers\MyFatoorahController::class, 'process'])->name('myfatoorah.process');
        Route::get('callback', [\App\Http\Controllers\MyFatoorahController::class, 'callback'])->name('myfatoorah.callback');
        Route::get('success', [\App\Http\Controllers\MyFatoorahController::class, 'success'])->name('myfatoorah.success');
        Route::get('error', [\App\Http\Controllers\MyFatoorahController::class, 'error'])->name('myfatoorah.error');
        Route::post('webhook', [\App\Http\Controllers\MyFatoorahController::class, 'webhook'])->middleware('myfatoorah.webhook')->name('myfatoorah.webhook');
        Route::get('status-poll/{subscription_id}', [\App\Http\Controllers\MyFatoorahController::class, 'statusPoll'])->name('myfatoorah.status-poll');
    });
});
