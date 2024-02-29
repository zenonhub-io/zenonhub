<?php

declare(strict_types=1);

namespace App\View\Components\Forms\Inputs;

use Illuminate\Contracts\View\View;

class Checkbox extends Input
{
    public function __construct(
        public string $name,
        public ?string $label = null,
        public ?string $id = null,
        public ?string $value = '1',
        public bool $required = false,
        public bool $readonly = false,
        public bool $disabled = false,
        public bool $checked = false,
        public bool $switch = false
    ) {
        parent::__construct($name, 'checkbox', $id, $value, $required, $readonly, $disabled);

        $this->checked = (bool) old($name, $checked);
    }

    public function render() : View
    {
        return view('components.forms.inputs.checkbox');
    }
}
