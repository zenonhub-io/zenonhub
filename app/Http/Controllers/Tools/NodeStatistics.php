<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\PageController;

class NodeStatistics extends PageController
{
    public function show()
    {
        $this->page['meta']['title'] = 'Node Statistics';
        $this->page['meta']['description'] = 'View the networks public node distribution and statistics';
        $this->page['data'] = [
            'component' => 'tools.node-statistics',
        ];
        return $this->render('pages/tools');
    }
}
