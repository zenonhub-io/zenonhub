<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use MetaTags;

class TermsController
{
    public function __invoke(): View
    {
        MetaTags::title(__('Terms & Conditions'))
            ->description(__('Read our Terms & Conditions to learn more about our policies and terms'))
            // ->canonical(route('terms'))
            ->metaByName('robots', 'index,nofollow');

        return view('terms');
    }
}
