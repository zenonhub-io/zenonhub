<?php

namespace App\Http\Livewire\Stats;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Nodes extends Component
{
    public string $updated;

    public string $tab = 'map';

    protected $queryString = [
        'tab' => ['except' => 'map'],
    ];

    protected $listeners = ['tabChange'];

    public function tabChange($tab = 'map')
    {
        $this->tab = $tab;
    }

    public function render()
    {
        $updated = Cache::get('node-data-updated');

        if ($updated) {
            $this->updated = Carbon::createFromTimestamp($updated)->format(config('zenon.date_format'));
        }

        return view('livewire.stats.nodes');
    }
}
