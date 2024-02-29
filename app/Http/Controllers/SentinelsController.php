<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use MetaTags;

class SentinelsController
{
    public function __invoke() : View
    {
        //        MetaTags::title('')
        //            ->description('');

        return view('pillars');
    }
}
