<?php

declare(strict_types=1);

namespace App\Http\Livewire\Pillars;

use App\Domains\Nom\Models\Pillar as PillarModel;
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
            'pillar' => PillarModel::firstWhere('slug', $this->slug),
        ]);
    }
}
