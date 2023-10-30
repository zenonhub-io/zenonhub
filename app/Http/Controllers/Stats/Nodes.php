<?php

namespace App\Http\Controllers\Stats;

use Meta;

class Nodes
{
    public function show()
    {
        Meta::title('Zenon Node Stats')
            ->description('Our Public node stats page displays the Zenon Network public node stats including their geographic distribution, version and network data');

        return view('pages/stats', [
            'view' => 'stats.nodes',
        ]);
    }
}
