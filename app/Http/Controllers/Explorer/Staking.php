<?php

namespace App\Http\Controllers\Explorer;

use App\Http\Controllers\PageController;

class Staking extends PageController
{
    public function show()
    {
        $this->page['meta']['title'] = 'Staking';
        $this->page['meta']['description'] = 'A list of all addresses actively staking tokens in the network.';
        $this->page['data'] = [
            'component' => 'explorer.staking',
        ];

        return $this->render('pages/explorer/overview');
    }
}
