<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use MetaTags;

class TermsController
{
    public function __invoke(): View
    {
        MetaTags::title('Terms & Conditions');

        return view('terms');
    }
}
