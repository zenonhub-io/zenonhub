<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tools;

use Illuminate\Contracts\View\View;
use MetaTags;

class ApiPlaygroundController
{
    public function __invoke(): View
    {
        MetaTags::title('API Playground')
            ->description('Explore and test the public RPC endpoints of the Zenon Network and see the results right in your browser')
            ->meta([
                'robots' => 'index,follow',
                'canonical' => route('tools.api-playground'),
            ]);

        return view('tools.api-playground');
    }
}
