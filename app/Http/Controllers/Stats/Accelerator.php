<?php

namespace App\Http\Controllers\Stats;

use App\Http\Controllers\PageController;

class Accelerator extends PageController
{
    public function show()
    {
        $this->page['meta']['title'] = 'Accelerator Z Stats';
        $this->page['meta']['description'] = 'Get an overview of the Accelerator Z projects and phases';
        $this->page['data'] = [
            'component' => 'stats.accelerator',
        ];

        return $this->render('pages/stats');
    }
}
