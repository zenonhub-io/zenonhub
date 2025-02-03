<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use MetaTags;

class InfoController
{
    public function __invoke(): View
    {
        MetaTags::title('About Zenon Hub', false)
            ->canonical(route('info'))
            ->metaByName('robots', 'index,follow');

        return view('info');
    }
}
