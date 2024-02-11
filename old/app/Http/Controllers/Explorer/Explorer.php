<?php

namespace App\Http\Controllers\Explorer;

use Meta;

class Explorer
{
    public function show()
    {
        Meta::title('Zenon Network Explorer')
            ->description('Explore and search the Zenon Network for momentums, transactions, addresses, tokens and other activities taking place on the Network of Momentum');

        return view('pages/explorer/overview');
    }
}
