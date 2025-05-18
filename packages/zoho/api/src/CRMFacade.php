<?php

namespace Zoho\Api;

use Illuminate\Support\Facades\Facade as LaravelFacade;
use packages\packages\zoho\api\src\CRM;

class CRMFacade extends LaravelFacade
{
    public static function getFacadeAccessor(): string
    {
        return CRM::class;
    }
}
