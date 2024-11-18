<?php

declare(strict_types=1);

namespace App\Livewire\Explorer;

use App\Models\Nom\Momentum;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class LatestMomentums extends Component
{
    public function render(): View
    {
        return view('livewire.explorer.latest-momentums', [
            'momentums' => Momentum::withCount('accountBlocks')
                ->limit(9)
                ->latest('height')
                ->get(),
        ]);
    }
}
