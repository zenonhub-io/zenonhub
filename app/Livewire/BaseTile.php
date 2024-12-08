<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

abstract class BaseTile extends Component
{
    public function placeholder(): View
    {
        return view('livewire.placeholders.loading');
    }
}
