<?php

namespace Zoho\API\Facades;

use Illuminate\Support\Facades\Facade as LaravelFacade;
use Zoho\API\Oauth2;

class OAuthFacade extends LaravelFacade
{
    public static function getFacadeAccessor(): string
    {
        return Oauth2::class;
    }
}
