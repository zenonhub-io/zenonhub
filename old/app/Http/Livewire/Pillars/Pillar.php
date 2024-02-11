<?php

namespace App\Http\Livewire\Pillars;

use App\Models\Nom\Pillar as PillarModel;
use Livewire\Component;

class Pillar extends Component
{
    public string $slug;

    public string $tab = 'delegators';

    protected $queryString = [
        'tab' => ['except' => 'delegators'],
    ];

    public function render()
    {
        return view('livewire.pillars.pillar', [
            'pillar' => PillarModel::findBySlug($this->slug),
        ]);
    }
}
