<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Repositories Bindings
    |--------------------------------------------------------------------------
    |
    | Define your interface to implementation mappings here.
    |
    */

    'bindings' => [
        \App\Repositories\Contracts\UserRepositoryInterface::class => \App\Repositories\Eloquent\UserRepository::class,
        \App\Repositories\Contracts\BannerRepositoryInterface::class => \App\Repositories\Eloquent\BannerRepository::class,
        \App\Repositories\Contracts\CategoryRepositoryInterface::class => \App\Repositories\Eloquent\CategoryRepository::class,
        \App\Repositories\Contracts\OfferRepositoryInterface::class => \App\Repositories\Eloquent\OfferRepository::class,
        \App\Repositories\Contracts\ProviderRepositoryInterface::class => \App\Repositories\Eloquent\ProviderRepository::class,
        \App\Repositories\Contracts\CityRepositoryInterface::class => \App\Repositories\Eloquent\CityRepository::class,
        \App\Repositories\Contracts\FavoriteRepositoryInterface::class => \App\Repositories\Eloquent\FavoriteRepository::class,
        \App\Repositories\Contracts\SectionRepositoryInterface::class => \App\Repositories\Eloquent\SectionRepository::class,
        \App\Repositories\Contracts\StoryRepositoryInterface::class => \App\Repositories\Eloquent\StoryRepository::class,
        \App\Repositories\Contracts\NationalityRepositoryInterface::class => \App\Repositories\Eloquent\NationalityRepository::class,
        \App\Repositories\Contracts\GovernorateRepositoryInterface::class => \App\Repositories\Eloquent\GovernorateRepository::class,
        \App\Repositories\Contracts\ReviewRepositoryInterface::class => \App\Repositories\Eloquent\ReviewRepository::class,
        \App\Repositories\Contracts\MemberPlanRepositoryInterface::class => \App\Repositories\Eloquent\MemberPlanRepository::class,
        \App\Repositories\Contracts\SubscriptionRepositoryInterface::class => \App\Repositories\Eloquent\SubscriptionRepository::class,
        \App\Repositories\Contracts\CardRequestRepositoryInterface::class => \App\Repositories\Eloquent\CardRequestRepository::class,
        \App\Repositories\Contracts\WalletTransactionRepositoryInterface::class => \App\Repositories\Eloquent\WalletTransactionRepository::class,
        \App\Repositories\Contracts\WithdrawalRepositoryInterface::class => \App\Repositories\Eloquent\WithdrawalRepository::class,
        \App\Repositories\Contracts\VisitRepositoryInterface::class => \App\Repositories\Eloquent\VisitRepository::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Repositories Singletons
    |--------------------------------------------------------------------------
    |
    | Define your interface to implementation mappings here.
    |
    */
    'singletons' => [
        \App\Repositories\Contracts\CountryRepositoryInterface::class => \App\Repositories\Eloquent\CountryRepository::class,
        \App\Services\CountryContext::class => \App\Services\CountryContext::class,
        \App\Services\CurrencyService::class => \App\Services\CurrencyService::class,
    ],
];
