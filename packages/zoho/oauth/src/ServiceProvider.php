<?php

namespace Zoho\Oauth;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/zoho.php', 'zoho'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        //
    }
}
