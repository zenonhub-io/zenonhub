<?php

declare(strict_types=1);

namespace App\View\Components\Navigation;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Dropdown extends Component
{
    public function render(): View
    {
        return view('components.navigation.dropdown');
    }
}
