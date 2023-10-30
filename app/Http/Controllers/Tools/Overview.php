<?php

namespace App\Http\Controllers\Tools;

use Meta;

class Overview
{
    public function show()
    {
        Meta::title('Tools');

        return view('pages/tools');
    }
}
