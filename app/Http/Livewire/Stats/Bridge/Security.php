<?php

namespace App\Http\Livewire\Stats\Bridge;

use App\Models\Nom\BridgeAdmin;
use App\Models\Nom\BridgeGuardian;
use App\Services\BridgeStatus;
use Illuminate\Support\Facades\App;
use Livewire\Component;

class Security extends Component
{
    public function render()
    {
        $bridgeStatus = App::make(BridgeStatus::class);
        $timeChallenges = collect($bridgeStatus->getTimeChallenges());

        return view('livewire.stats.bridge.security', [
            'adminDelay' => $bridgeStatus->getAdminDelay(),
            'softDelay' => $bridgeStatus->getSoftDelay(),
            'activeTimeChallenges' => $timeChallenges->where('isActive', true),
            'timeChallenges' => $timeChallenges,
            'guardians' => BridgeGuardian::allActive()->get()->sortByDesc('account.updated_at'),
            'admin' => BridgeAdmin::getActiveAdmin(),
        ]);
    }
}
