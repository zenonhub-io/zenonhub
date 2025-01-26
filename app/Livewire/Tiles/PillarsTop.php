<?php

declare(strict_types=1);

namespace App\Livewire\Tiles;

use App\Livewire\BaseComponent;
use App\Models\Nom\Pillar;

class PillarsTop extends BaseComponent
{
    public function render()
    {
        return view('livewire.tiles.pillars-top', [
            'pillars' => Pillar::with('socialProfile')
                ->whereActive()
                ->orderBy('rank')
                ->limit(5)
                ->get(),
        ]);
    }
}
