<?php

declare(strict_types=1);

namespace App\View\Components\Forms\Inputs;

use Illuminate\Contracts\View\View;

class Hidden extends Input
{
    public function __construct(
        public string $name,
        public ?string $id = null,
        public ?string $value = '',
    ) {
        parent::__construct($name, 'hidden', $id, $value);
    }

    public function render(): View
    {
        return view('components.forms.inputs.hidden');
    }
}
