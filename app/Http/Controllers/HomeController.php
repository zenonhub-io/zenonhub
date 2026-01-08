<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use MetaTags;

class HomeController
{
    public function __invoke(): View
    {
        MetaTags::title(__('Zenon Hub | Explore the Zenon Network (Network of Momentum)'), false)
            ->description(__('Discover Zenon Hub – your portal to the innovative Zenon Network. Track blocks, addresses, tokens, and explore activity on the cutting-edge Network of Momentum'))
            ->twitterImage(asset('build/img/meta-big.png'))
            ->openGraphImage(asset('build/img/meta-big.png'))
            ->canonical(route('home'))
            ->metaByName('robots', 'index,follow');

        return view('home');
    }
}
