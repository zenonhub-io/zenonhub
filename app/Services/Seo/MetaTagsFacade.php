<?php

declare(strict_types=1);

namespace App\Services\Seo;

use Illuminate\Support\Facades\Facade;

class MetaTagsFacade extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return MetaTags::class;
    }
}
