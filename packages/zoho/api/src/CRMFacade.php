<?php

namespace Zoho\Api;

use Illuminate\Support\Facades\Facade as LaravelFacade;

class CRMFacade extends LaravelFacade
{
    public static function getFacadeAccessor(): string
    {
        return CRM::class;
    }
}
