<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use MetaTags;

class AdvertisingController
{
    public function __invoke(): View
    {
        MetaTags::title(__('Advertise on Zenon Hub: Reach Zenon Enthusiasts'), false)
            ->description(__('Promote your brand or product on Zenon Hub and connect with a dedicated community of Zenon enthusiasts'))
            ->canonical(route('advertise'))
            ->metaByName('robots', 'index,nofollow');

        return view('advertise');
    }
}
