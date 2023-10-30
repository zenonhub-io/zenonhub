<?php

namespace App\Http\Controllers\Stats;

use Meta;

class Bridge
{
    public function show()
    {
        Meta::title('Bridge Stats')
            ->description('The Bridge Stats page shows a detailed overview of the Multi-Chain Bridge including its status, admin actions, security info and supoorted networks');

        return view('pages/stats', [
            'view' => 'stats.bridge',
        ]);
    }
}
