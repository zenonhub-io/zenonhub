<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\PageController;

class PublicNodes extends PageController
{
    public function show()
    {
        $this->page['meta']['title'] = 'Public Nodes';

        return $this->render('pages/services/public-nodes');
    }
}
