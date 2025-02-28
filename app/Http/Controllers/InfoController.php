<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use MetaTags;

class InfoController
{
    public function __invoke(): View
    {
        MetaTags::title(__('About Zenon Hub: Our Mission & Vision'), false)
            ->description('Learn more about Zenon Hub, our mission, vision, and commitment to advancing blockchain technology and community innovation')
            ->canonical(route('info'))
            ->metaByName('robots', 'index,follow');

        return view('info');
    }
}
