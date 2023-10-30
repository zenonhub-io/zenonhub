<?php

namespace App\Http\Controllers\Stats;

use Meta;

class Overview
{
    public function show()
    {
        Meta::title('Zenon Network Stats');

        return view('pages/stats');
    }
}
