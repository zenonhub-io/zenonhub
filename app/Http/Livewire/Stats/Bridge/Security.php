<?php

namespace App\Http\Livewire\Stats\Bridge;

use Illuminate\Support\Facades\App;
use Livewire\Component;

class Security extends Component
{
    public bool $shouldLoad = false;

    public ?array $guardians;

    public ?array $timeChallenges;

    public function render()
    {
        return view('livewire.stats.bridge.security');
    }

    public function loadSecurityData()
    {
        $znn = App::make('zenon.api');
        $this->shouldLoad = true;
        $this->guardians = (array) $znn->bridge->getSecurityInfo()['data'];
        $this->timeChallenges = $znn->bridge->getTimeChallengesInfo()['data']->list;
    }
}