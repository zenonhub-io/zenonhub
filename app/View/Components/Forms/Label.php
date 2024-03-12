<?php

declare(strict_types=1);

namespace App\View\Components\Forms;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Label extends Component
{
    public function __construct(
        public string $label,
        public ?string $for = null
    ) {
    }

    public function render(): View
    {
        return view('components.forms.label');
    }
}
