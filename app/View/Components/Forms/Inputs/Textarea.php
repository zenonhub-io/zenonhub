<?php

declare(strict_types=1);

namespace App\View\Components\Forms\Inputs;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Textarea extends Component
{
    public function __construct(
        public string $name,
        public int $rows = 3,
        public ?string $id = null,
        public ?string $value = '',
        public bool $required = false,
        public bool $readonly = false,
        public bool $disabled = false,
    ) {
        $this->name = strtolower($name);
        $this->id = $id ?? $name;
        $this->value = old($name, $value ?? '');
    }

    public function render(): View
    {
        return view('components.forms.inputs.textarea');
    }
}
