<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use MetaTags;

class DonateController
{
    public function __invoke(): View
    {
        MetaTags::title('Donate to Zenon Hub', false);

        return view('donate');
    }
}
