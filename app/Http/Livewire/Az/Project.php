<?php

namespace App\Http\Livewire\Az;

use App\Models\Nom\AcceleratorProject;
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
            'project' => AcceleratorProject::findByHash($this->hash)
        ]);
    }
}
