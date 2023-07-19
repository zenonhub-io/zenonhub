<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\PageController;

class Nodes extends PageController
{
    public function show()
    {
        $this->page['meta']['title'] = 'Public Nodes';

        return $this->render('pages/nodes');
    }
}
