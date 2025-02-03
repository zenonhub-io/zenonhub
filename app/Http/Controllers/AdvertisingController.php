<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use MetaTags;

class AdvertisingController
{
    public function __invoke(): View
    {
        MetaTags::title('Advertise on Zenon Hub', false)
            ->canonical(route('advertise'))
            ->metaByName('robots', 'index,nofollow');

        return view('advertise');
    }
}
