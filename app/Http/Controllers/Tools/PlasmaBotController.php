<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tools;

use Illuminate\Contracts\View\View;
use MetaTags;

class PlasmaBotController
{
    public function __invoke(): View
    {
        MetaTags::title('Plasma Bot')
            ->description('Use the plasma bot tool to fuse some plasma to you address allowing for faster feeless transactions')
            ->meta([
                'robots' => 'index,follow',
                'canonical' => route('tools.plasma-bot'),
            ]);

        return view('tools.plasma-bot');
    }
}
