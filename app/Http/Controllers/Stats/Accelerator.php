<?php

namespace App\Http\Controllers\Stats;

use Meta;

class Accelerator
{
    public function show()
    {
        Meta::title('Accelerator Z Stats')
            ->description('Get an overview of the Accelerator Z contract, projects, engagement and contributors');

        return view('pages/stats', [
            'view' => 'stats.accelerator',
        ]);
    }
}
