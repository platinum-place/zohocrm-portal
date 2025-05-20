<?php

namespace Zoho\API\Facades;

use Illuminate\Support\Facades\Facade as LaravelFacade;
use Zoho\API\CRM;

class CRMFacade extends LaravelFacade
{
    public static function getFacadeAccessor(): string
    {
        return CRM::class;
    }
}
