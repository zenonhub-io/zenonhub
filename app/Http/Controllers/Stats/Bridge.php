<?php

namespace App\Http\Controllers\Stats;

use App\Http\Controllers\PageController;

class Bridge extends PageController
{
    public function show()
    {
        $this->page['meta']['title'] = 'Bridge Stats';
        $this->page['data'] = [
            'component' => 'stats.bridge',
        ];

        return $this->render('pages/stats');
    }
}
