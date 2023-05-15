<?php

namespace App\Http\Controllers\Stats;

use App\Http\Controllers\PageController;

class Overview extends PageController
{
    public function show()
    {
        $this->page['meta']['title'] = 'Network Stats';

        return $this->render('pages/stats');
    }
}
