<?php

declare(strict_types=1);

namespace App\View\Components\Navbar;

use Illuminate\Contracts\View\View;

class DropdownItem extends BaseItem
{
    public function render() : View
    {
        return view('components.navbar.dropdown-item');
    }
}
