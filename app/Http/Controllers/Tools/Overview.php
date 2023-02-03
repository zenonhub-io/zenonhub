<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\PageController;

class Overview extends PageController
{
    public function show()
    {
        $this->page['meta']['title'] = 'Network Tools';
        return $this->render('pages/tools');
    }
}
