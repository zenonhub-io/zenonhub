<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Meta;

class Privacy
{
    public function show()
    {
        Meta::title('Privacy policy');

        return view('pages/privacy');
    }
}
