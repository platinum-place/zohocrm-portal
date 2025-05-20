<?php

namespace App\Providers;

use App\Exceptions\Handler;
use App\Models\User\Client;
use Carbon\CarbonInterval;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (!config('app.debug')) {
            $this->app->singleton(
                ExceptionHandler::class,
                Handler::class
            );
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Passport::tokensExpireIn(CarbonInterval::days(15));
        Passport::refreshTokensExpireIn(CarbonInterval::days(30));
        Passport::personalAccessTokensExpireIn(CarbonInterval::months(6));

        Passport::useClientModel(Client::class);

        Passport::enablePasswordGrant();
    }
}
