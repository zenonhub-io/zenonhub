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
            ->meta([
                'robots' => 'index,follow',
                'canonical' => route('policy'),
            ]);

        return view('policy');
    }
}
