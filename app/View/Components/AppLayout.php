<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AppLayout extends Component
{
    public function render(): View
    {
        if (is_hqz()) {
            return view('layouts.app-hqz');
        }

        return view('layouts.app-nom');
    }
}
