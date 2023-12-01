<?php

namespace App\Http\Livewire\Stats\Bridge;

use App\Services\ZenonSdk;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Security extends Component
{
    public ?array $guardians;

    public ?array $timeChallenges;

    public function render()
    {
        return view('livewire.stats.bridge.security');
    }

    public function loadSecurityData()
    {
        $cacheKey = 'nom.bridgeStats.securityData';

        try {
            $znn = App::make(ZenonSdk::class);
            $data = [
                'guardians' => (array) $znn->bridge->getSecurityInfo()['data'],
                'timeChallenges' => $znn->bridge->getTimeChallengesInfo()['data']->list,
            ];
            Cache::forever($cacheKey, $data);
        } catch (\Throwable $throwable) {
            $data = Cache::get($cacheKey);
        }

        $this->guardians = $data['guardians'];
        $this->timeChallenges = $data['timeChallenges'];
    }
}
