<?php

declare(strict_types=1);

namespace App\View\Components\Forms\Inputs;

use Illuminate\Contracts\View\View;

class Radio extends Input
{
    public function __construct(
        public string $label,
        public string $name,
        public ?string $id = null,
        public ?string $value = '',
        public bool $required = false,
        public bool $readonly = false,
        public bool $disabled = false,
        public bool $selected = false,
    ) {
        parent::__construct($name, 'radio', $id, $value, $required, $readonly, $disabled);

        $this->selected = (bool) old($name, $selected);
    }

    public function render() : View
    {
        return view('components.forms.inputs.radio');
    }
}
