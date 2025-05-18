<?php

namespace Zoho\Api;

use Illuminate\Support\Facades\Facade as LaravelFacade;

class OAuthFacade extends LaravelFacade
{
    public static function getFacadeAccessor(): string
    {
        return Oauth2::class;
    }
}
