<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tools;

use Illuminate\Contracts\View\View;
use MetaTags;

class PlasmaBotController
{
    public function __invoke(): View
    {
        MetaTags::title(__('Plasma Bot: Fuse Plasma for Feeless Transactions'))
            ->description(__('Use the plasma bot tool to fuse some plasma to you address allowing for faster feeless transactions'))
            ->canonical(route('tools.plasma-bot'))
            ->metaByName('robots', 'index,nofollow');

        return view('tools.plasma-bot', [
            'enabled' => config('services.plasma-bot.enabled'),
        ]);
    }
}
