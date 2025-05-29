<?php

namespace App\Providers;

use App\Contracts\SunatInterface;
use App\Services\Sunat\SunatServices;
use Illuminate\Support\ServiceProvider;

class GreenterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(SunatInterface::class, function ($app) {
            return new SunatServices();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
