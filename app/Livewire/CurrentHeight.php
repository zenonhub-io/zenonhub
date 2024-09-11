<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Nom\Momentum;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CurrentHeight extends Component
{
    public function render(): View
    {
        return view('livewire.current-height', [
            'height' => Momentum::max('height'),
        ]);
    }
}
