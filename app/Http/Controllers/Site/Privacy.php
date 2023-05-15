<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\PageController;

class Privacy extends PageController
{
    public function show()
    {
        $this->page['meta']['title'] = 'Privacy policy';

        return $this->render('pages/privacy');
    }
}
