<?php

declare(strict_types=1);

namespace App\View\Components\Forms;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Group extends Component
{
    public function __construct(
        public ?string $name = null,
        public ?string $label = null,
        public ?string $type = 'text',
        public ?string $helpText = null,
        public ?string $id = null,
        public mixed $value = ''
    ) {
        $this->name = $name ? strtolower($name) : '';
        $this->id = $id ?? $name;
        $this->value = old($name, $value ?? '');
    }

    public function render() : View
    {
        return view('components.forms.group');
    }
}
