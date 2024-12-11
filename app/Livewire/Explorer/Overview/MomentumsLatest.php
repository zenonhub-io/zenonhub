<?php

declare(strict_types=1);

namespace App\Livewire\Explorer\Overview;

use App\Livewire\BaseComponent;
use App\Models\Nom\Momentum;
use Illuminate\Contracts\View\View;

class MomentumsLatest extends BaseComponent
{
    public function render(): View
    {
        return view('livewire.explorer.overview.momentums-latest', [
            'momentums' => Momentum::with('producerPillar')
                ->withCount('accountBlocks')
                ->limit(6)
                ->latest('height')
                ->get(),
        ]);
    }
}
