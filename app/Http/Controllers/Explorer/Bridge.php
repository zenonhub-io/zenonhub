<?php

namespace App\Http\Controllers\Explorer;

use Meta;

class Bridge
{
    public function show()
    {
        Meta::title('Bridge inbound & outbound transactions')
            ->description('A list of all incoming and outgoing bridge transactions, showing sender and receiver addresses, amount and network');

        return view('pages/explorer/overview', [
            'view' => 'explorer.bridge',
        ]);
    }
}
