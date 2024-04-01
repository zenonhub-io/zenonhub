<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Domains\Nom\Models\Account;
use Closure;
use Illuminate\View\Component;

class Address extends Component
{
    public Account $account;

    public bool $named;

    public bool $linked;

    public bool $alwaysShort;

    public int $eitherSide;

    public string $breakpoint;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        Account $account,
        bool $named = true,
        bool $linked = true,
        bool $alwaysShort = false,
        int $eitherSide = 10,
        string $breakpoint = 'md'
    ) {
        $this->account = $account;
        $this->named = $named;
        $this->linked = $linked;
        $this->alwaysShort = $alwaysShort;
        $this->eitherSide = $eitherSide;
        $this->breakpoint = $breakpoint;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|Closure|string
     */
    public function render()
    {
        return view('components.utilities.address');
    }
}
