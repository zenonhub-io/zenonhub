<?php

namespace App\View\Components;

use Illuminate\View\Component;

class HashTooltip extends Component
{
    public string $hash;

    public int $eitherSide;

    public string $breakpoint;

    public bool $alwaysShort;

    public function __construct(string $hash, int $eitherSide = 10, string $breakpoint = 'md', bool $alwaysShort = false)
    {
        $this->hash = $hash;
        $this->eitherSide = $eitherSide;
        $this->breakpoint = $breakpoint;
        $this->alwaysShort = $alwaysShort;
    }

    public function render()
    {
        return view('components.utilities.hash-tooltip');
    }
}
