<?php

declare(strict_types=1);

namespace App\Http\Controllers\Explorer;

use Illuminate\Contracts\View\View;
use MetaTags;

class ExplorerOverviewController
{
    public function __invoke(): View
    {
        MetaTags::title('Zenon Hub | Explore the Zenon Network Blockchain with Ease', false);

        return view('explorer/overview');
    }
}
