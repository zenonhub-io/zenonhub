<?php

declare(strict_types=1);

namespace App\Http\Controllers\Services;

use Illuminate\Contracts\View\View;
use MetaTags;

class PublicNodesController
{
    public function __invoke(): View
    {
        MetaTags::title('Public Nodes')
            ->canonical(route('services.public-nodes'))
            ->metaByName('robots', 'index,nofollow');

        return view('services.public-nodes');
    }
}
