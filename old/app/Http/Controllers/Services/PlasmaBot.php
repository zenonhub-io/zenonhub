<?php

namespace App\Http\Controllers\Services;

use Meta;

class PlasmaBot
{
    public function show()
    {
        Meta::title('Plasma Bot');

        return view('pages/services/plasma-bot');
    }
}
