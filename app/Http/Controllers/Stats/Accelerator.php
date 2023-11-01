<?php

namespace App\Http\Controllers\Stats;

use Meta;

class Accelerator
{
    public function show()
    {
        Meta::title('Accelerator Z Stats')
            ->description('The Accelerator-Z Stats page shows an overview of the Accelerator Z embedded smart contract, projects, pillar voting engagement and contributors');

        return view('pages/stats', [
            'view' => 'stats.accelerator',
        ]);
    }
}
