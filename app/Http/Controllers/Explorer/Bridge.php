<?php

namespace App\Http\Controllers\Explorer;

use Meta;

class Bridge
{
    public function show()
    {
        Meta::title('Bridge wraps & unwraps')
            ->description('A list of all bridge wraps and unwraps sorted by datetime order');

        return view('pages/explorer/overview', [
            'view' => 'explorer.bridge',
        ]);
    }
}
