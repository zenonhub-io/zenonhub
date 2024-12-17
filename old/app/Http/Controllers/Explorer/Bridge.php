<?php

declare(strict_types=1);

namespace App\Http\Controllers\Explorer;

use Meta;

class Bridge
{
    public function show()
    {
        Meta::title('Bridge Inbound & Outbound Transactions')
            ->description('A list of all incoming and outgoing bridge transactions, showing sender and receiver addresses, amount and network');

        return view('pages/explorer/overview', [
            'view' => 'explorer.bridge',
        ]);
    }
}
