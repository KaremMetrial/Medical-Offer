<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $bindings = config('repositories.bindings', []);
        $singletons = config('repositories.singletons', []);

        foreach ($singletons as $interface => $implementation) {
            $this->app->singleton($interface, $implementation);
        }
        foreach ($bindings as $interface => $implementation) {
            $this->app->bind($interface, $implementation);
        }

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
