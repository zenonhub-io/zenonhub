<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\PageController;

class PlasmaBot extends PageController
{
    public function show()
    {
        $this->page['meta']['title'] = 'Plasma Bot';
        $this->page['meta']['description'] = 'Fuse some plasma to you address to speed up transactions';
        $this->page['data'] = [
            'component' => 'tools.plasma-bot',
        ];

        return $this->render('pages/tools');
    }
}
