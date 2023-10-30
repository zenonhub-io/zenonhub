<?php

namespace App\Http\Controllers\Stats;

use Meta;

class Bridge
{
    public function show()
    {
        Meta::title('Bridge Stats')
            ->description('View the Multi-chain Bridge status, orchestrators, actions and more');

        return view('pages/stats', [
            'view' => 'stats.bridge',
        ]);
    }
}
