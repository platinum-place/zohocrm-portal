<?php

namespace Zoho\CRM;

use Illuminate\Support\Facades\Facade as LaravelFacade;
use Zoho\CRM\Services\CRMService;

class Facade extends LaravelFacade
{
    public static function getFacadeAccessor(): string
    {
        return CRMService::class;
    }
}
