<?php

namespace App\Http\Livewire\Services;

use Illuminate\Support\Facades\App;
use Livewire\Component;

class PublicNodes extends Component
{
    public string $tab = 'process';

    protected $queryString = [
        'tab' => ['except' => 'process'],
    ];

    protected $listeners = ['showTab'];

    public function showTab($tab)
    {
        $this->tab = $tab;
    }

    public function render()
    {
        return view('livewire.services.public-nodes', $this->loadData());
    }

    private function loadData()
    {
        $znn = App::make('zenon.api');

        if ($this->tab === 'process') {
            return $znn->stats->processInfo();
        }

        if ($this->tab === 'sync') {
            return $znn->stats->syncInfo();
        }

        if ($this->tab === 'network') {
            return $znn->stats->networkInfo();
        }
    }
}
