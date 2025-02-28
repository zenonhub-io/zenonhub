<?php

declare(strict_types=1);

namespace App\Http\Controllers\Services;

use Illuminate\Contracts\View\View;
use MetaTags;

class PublicNodesController
{
    public function __invoke(): View
    {
        MetaTags::title(__('Public RPC Nodes: Secure and Free access to the Zenon Network'))
            ->description(__('Connect to our secure public RPC nodes to interact with the Zenon Network quickly and easily'))
            ->canonical(route('services.public-nodes'))
            ->metaByName('robots', 'index,nofollow');

        return view('services.public-nodes');
    }
}
