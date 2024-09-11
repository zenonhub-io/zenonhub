<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use MetaTags;

class SentinelsController
{
    private string $defaultTab = 'all';

    public function __invoke(?string $tab = null): View
    {
        //        MetaTags::title('')
        //            ->description('');

        return view('sentinels.list', [
            'tab' => $tab ?: $this->defaultTab,
        ]);
    }
}
