<?php

namespace App\Http\Controllers\Explorer;

use App\Http\Controllers\PageController;

class Fusions extends PageController
{
    public function show()
    {
        $this->page['meta']['title'] = 'QSR Fusions';
        $this->page['meta']['description'] = 'A list of all the addresses currently fusing QSR into plasma. Plasma is used to speed up transactions by bypassing the POW';
        $this->page['data'] = [
            'component' => 'explorer.fusions',
        ];

        return $this->render('pages/explorer/overview');
    }
}
