<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Alert extends Component
{
    /**
     * The alert message.
     */
    public ?string $message;

    /**
     * The alert type.
     *
     * @var ?string
     */
    public ?string $type;

    /**
     * The alert icon, if any.
     *
     * @var ?string
     */
    public ?string $icon;

    /**
     * If the alert shows a close icon
     */
    public bool $closeButton = false;

    /**
     * Create the component instance.
     *
     * @param  ?string  $type
     * @param  ?string  $icon
     * @return void
     */
    public function __construct(?string $message = null, ?string $type = null, ?string $icon = null, bool $closeButton = false)
    {
        $this->message = $message;
        $this->type = $type;
        $this->icon = $icon;
        $this->closeButton = $closeButton;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.utilities.alert');
    }
}
