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
    ],
];
