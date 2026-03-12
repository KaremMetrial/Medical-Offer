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
        \App\Repositories\Contracts\CountryRepositoryInterface::class => \App\Repositories\Eloquent\CountryRepository::class,
        \App\Repositories\Contracts\CityRepositoryInterface::class => \App\Repositories\Eloquent\CityRepository::class,
        \App\Repositories\Contracts\FavoriteRepositoryInterface::class => \App\Repositories\Eloquent\FavoriteRepository::class,
        \App\Repositories\Contracts\SectionRepositoryInterface::class => \App\Repositories\Eloquent\SectionRepository::class,
    ],
];
