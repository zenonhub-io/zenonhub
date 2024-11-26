<?php

declare(strict_types=1);

namespace App\Http\Controllers\Explorer;

use Illuminate\Contracts\View\View;
use MetaTags;

class MomentumListController
{
    public function __invoke(?string $tab = null): View
    {
        MetaTags::title('Momentums')
            ->description('A list of the latest confirmed Momentums (blocks) on the Zenon Network. The timestamp, producer, number of transactions and hash are shown in the list');

        return view('explorer.momentum-list');
    }
}
