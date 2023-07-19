<?php

namespace App\Http\Livewire\Site;

use Illuminate\Support\Facades\App;
use Livewire\Component;

class PublicNodes extends Component
{
    public string $tab = 'sync';

    protected $queryString = [
        'tab' => ['except' => 'sync'],
    ];

    protected $listeners = ['showTab'];

    public function showTab($tab)
    {
        $this->tab = $tab;
    }

    public function render()
    {
        return view('livewire.site.public-nodes', $this->loadData());
    }

    private function loadData()
    {
        $znn = App::make('zenon.api');

        if ($this->tab === 'sync') {
            return $znn->stats->syncInfo();
        }

        if ($this->tab === 'process') {
            return $znn->stats->processInfo();
        }

        if ($this->tab === 'network') {
            return $znn->stats->networkInfo();
        }
    }
}
