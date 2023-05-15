<?php

namespace App\Http\Controllers\Stats;

use App\Http\Controllers\PageController;

class Nodes extends PageController
{
    public function show()
    {
        $this->page['meta']['title'] = 'Node Stats';
        $this->page['meta']['description'] = 'View the networks public node distribution and statistics';
        $this->page['data'] = [
            'component' => 'stats.nodes',
        ];

        return $this->render('pages/stats');
    }
}
