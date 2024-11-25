<?php

declare(strict_types=1);

namespace App\Livewire\Explorer\Overview;

use App\Models\Nom\Momentum;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class LatestMomentums extends Component
{
    public function render(): View
    {
        return view('livewire.explorer.overview.latest-momentums', [
            'momentums' => Momentum::with('producerPillar')
                ->withCount('accountBlocks')
                ->limit(6)
                ->latest('height')
                ->get(),
        ]);
    }
}
