<?php

namespace App\Http\Controllers\Explorer;

use App\Http\Controllers\PageController;

class Staking extends PageController
{
    public function show()
    {
        $this->page['meta']['title'] = 'ZNN Staking';
        $this->page['meta']['description'] = 'A list of all the addresses actively staking ZNN. Staking locks ZNN for a period of time and generates QSR';
        $this->page['data'] = [
            'component' => 'explorer.staking',
        ];

        return $this->render('pages/explorer/overview');
    }
}
