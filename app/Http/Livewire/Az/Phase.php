<?php

namespace App\Http\Livewire\Az;

use App\Models\Nom\AcceleratorPhase;
use Livewire\Component;

class Phase extends Component
{
    public string $hash;
    public string $tab = 'votes';
    protected $queryString = [
        'tab' => ['except' => 'votes'],
    ];

    public function render()
    {
        return view('livewire.az.phase', [
            'phase' => AcceleratorPhase::findByHash($this->hash)
        ]);
    }
}
