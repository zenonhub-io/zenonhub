<?php

declare(strict_types=1);

namespace App\View\Components\Buttons;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FormButton extends Component
{
    public ?string $action;

    public string $method;

    public function __construct(?string $action = null, string $method = 'POST')
    {
        $this->action = $action;
        $this->method = strtoupper($method);
    }

    public function render(): View
    {
        return view('components.buttons.form-button');
    }
}
