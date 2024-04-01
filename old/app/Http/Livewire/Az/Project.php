<?php

declare(strict_types=1);

namespace App\Http\Livewire\Az;

use App\Domains\Nom\Models\AcceleratorProject;
use Livewire\Component;

class Project extends Component
{
    public string $hash;

    public string $tab = 'votes';

    protected $queryString = [
        'tab' => ['except' => 'votes'],
    ];

    public function render()
    {
        return view('livewire.az.project', [
            'project' => AcceleratorProject::findBy('hash', $this->hash),
        ]);
    }
}
