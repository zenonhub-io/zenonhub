<?php

namespace App\Http\Controllers\Explorer;

use App\Http\Controllers\PageController;

class Explorer extends PageController
{
    public function show()
    {
        $this->page['meta']['title'] = 'Zenon Network Explorer';
        $this->page['meta']['description'] = 'Explore and search the Zenon Network for momentums, transactions, addresses, tokens and other activities taking place on the Network of Momentum';

        return $this->render('pages/explorer/overview');
    }
}
