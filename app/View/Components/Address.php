<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Models\Nom\Account;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Address extends Component
{
    public function __construct(
        public Account $account,
        public bool $named = true,
        public bool $linked = true,
        public bool $alwaysShort = false,
        public int $eitherSide = 10,
        public string $breakpoint = 'md'
    ) {
    }

    public function render(): View
    {
        return view('components.address');
    }
}
