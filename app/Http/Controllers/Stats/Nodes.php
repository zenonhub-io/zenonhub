<?php

namespace App\Http\Controllers\Stats;

use Meta;

class Nodes
{
    public function show()
    {
        Meta::title('Node Stats')
            ->description('View the networks public node distribution and statistics');

        return view('pages/stats', [
            'view' => 'stats.nodes',
        ]);
    }
}
