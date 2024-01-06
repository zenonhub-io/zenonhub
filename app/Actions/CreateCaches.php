<?php

namespace App\Actions;

use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Fusion;
use App\Models\Nom\Momentum;
use App\Models\Nom\PillarDelegator;
use App\Models\Nom\Stake;
use Illuminate\Support\Facades\Cache;

class CreateCaches
{
    public function execute()
    {
        Cache::put('transaction-count', AccountBlock::count());
        Cache::put('address-count', Account::count());
        Cache::put('momentum-count', Momentum::count());

        $delegators = PillarDelegator::isActive()->whereHas('account', fn ($query) => $query->where('znn_balance', '>', '0'))->count();
        Cache::put('delegators-count', $delegators);

        $fusedQsr = qsr_token()->getDisplayAmount(Fusion::isActive()->sum('amount'));
        Cache::put('fused-qsr', $fusedQsr);

        $stakedZnn = znn_token()->getDisplayAmount(Stake::isActive()->isZnn()->sum('amount'), 0);
        Cache::put('staked-znn', $stakedZnn);
    }
}
