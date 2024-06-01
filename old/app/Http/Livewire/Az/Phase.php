<?php

declare(strict_types=1);

namespace App\Http\Livewire\Az;

use App\Domains\Nom\Models\AcceleratorPhase;
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
            'phase' => AcceleratorPhase::firstWhere('hash', $this->hash),
        ]);
    }
}
