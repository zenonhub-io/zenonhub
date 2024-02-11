<?php

namespace App\Http\Controllers\Services;

use Meta;

class Overview
{
    public function show()
    {
        Meta::title('Services');

        return view('pages/services');
    }
}
