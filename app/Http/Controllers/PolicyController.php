<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use MetaTags;

class PolicyController
{
    public function __invoke(): View
    {
        MetaTags::title('Privacy Policy')
            ->description('Understand how Zenon Hub collects, uses, and protects your data in compliance with privacy regulations')
            ->canonical(route('policy'))
            ->metaByName('robots', 'index,nofollow');

        return view('policy');
    }
}
