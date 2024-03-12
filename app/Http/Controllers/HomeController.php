<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use MetaTags;

class HomeController
{
    public function __invoke(): View
    {
        MetaTags::title('Zenon Hub | Explore the Zenon Network Blockchain with Ease', false)
            ->twitterImage(url('img/meta-big.png'))
            ->openGraphImage(url('img/meta-big.png'));

        return view('home');
    }
}
