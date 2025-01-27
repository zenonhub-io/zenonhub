<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use MetaTags;

class HomeController
{
    public function __invoke(): View
    {
        MetaTags::title('Zenon Hub | Explore the Zenon Network (Network of Momentum)', false)
            ->twitterImage(asset('build/img/meta-big.png'))
            ->openGraphImage(asset('build/img/meta-big.png'));

        return view('home');
    }
}
