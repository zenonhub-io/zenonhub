<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\PageController;

class PlasmaBot extends PageController
{
    public function show()
    {
        $this->page['meta']['title'] = 'Plasma Bot';

        return $this->render('pages/services/plasma-bot');
    }
}
