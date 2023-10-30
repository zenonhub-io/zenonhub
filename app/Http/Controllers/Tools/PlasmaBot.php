<?php

namespace App\Http\Controllers\Tools;

use Meta;

class PlasmaBot
{
    public function show()
    {
        Meta::title('Plasma Bot')
            ->description('Fuse some plasma to you address to speed up transactions');

        return view('pages/tools', [
            'view' => 'tools.plasma-bot',
        ]);
    }
}
