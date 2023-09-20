<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\PageController;

class Overview extends PageController
{
    public function show()
    {
        $this->page['meta']['title'] = 'Zenon Hub Services';

        return $this->render('pages/services');
    }
}
