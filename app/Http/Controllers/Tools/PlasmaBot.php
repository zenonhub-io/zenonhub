<?php

namespace App\Http\Controllers\Tools;

use Meta;

class PlasmaBot
{
    public function show()
    {
        Meta::title('Plasma Bot')
            ->description('Use the plasma bot tool to fuse some plasma to you address allowing for faster feeless transactions');

        return view('pages/tools', [
            'view' => 'tools.plasma-bot',
        ]);
    }
}
