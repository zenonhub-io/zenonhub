<?php

declare(strict_types=1);

namespace App\Actions;

use App\Domains\Nom\Models\Account;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Momentum;
use App\Domains\Nom\Models\PillarDelegator;
use App\Domains\Nom\Models\Plasma;
use App\Domains\Nom\Models\Stake;
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

        $fusedQsr = qsr_token()->getFormattedAmount(Plasma::isActive()->sum('amount'));
        Cache::put('fused-qsr', $fusedQsr);

        $stakedZnn = znn_token()->getFormattedAmount(Stake::isActive()->isZnn()->sum('amount'), 0);
        Cache::put('staked-znn', $stakedZnn);
    }
}
