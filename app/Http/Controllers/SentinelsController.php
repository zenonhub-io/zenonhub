<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use MetaTags;

class SentinelsController
{
    public function __invoke(?string $tab = 'all'): View
    {
        //        MetaTags::title('')
        //            ->description('')
        //            ->meta([
        //                'robots' => 'index,follow',
        //                'canonical' => route('sentinels', ['tab' => $tab]),
        //            ]);

        return view('sentinels.list', [
            'tab' => $tab,
        ]);
    }
}
