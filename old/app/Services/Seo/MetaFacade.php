<?php

namespace App\Services\Seo;

use App\Services\Meta;
use Illuminate\Support\Facades\Facade;

class MetaFacade extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return Meta::class;
    }
}
