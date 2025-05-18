<?php

namespace Zoho\Oauth;

use Illuminate\Support\Facades\Facade as LaravelFacade;
use Zoho\Oauth\Services\OauthService;

class Facade extends LaravelFacade
{
    public static function getFacadeAccessor(): string
    {
        return OauthService::class;
    }
}
