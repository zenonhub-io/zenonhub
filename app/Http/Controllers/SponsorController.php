<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use MetaTags;

class SponsorController
{
    public function __invoke(): View
    {
        MetaTags::title('Sponsor Zenon Hub', false);

        return view('sponsor');
    }
}
