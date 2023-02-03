<?php

namespace App\Console\Commands;

use Artisan;
use Cache;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Momentum;
use App\Models\Nom\PillarDelegator;
use App\Models\Nom\Fusion;
use App\Models\Nom\Staker;
use Illuminate\Console\Command;

class CreateCaches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zenon:create-caches';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates the main caches used by the explorer';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Cache::put('transaction-count', (AccountBlock::count() - 1));
        Cache::put('address-count', Account::count());
        Cache::put('momentum-count', Momentum::max('height'));

        $delegators = PillarDelegator::isActive()->whereHas('account', fn($query) => $query->where('znn_balance', '>', '0'))->count();
        Cache::put('delegators-count', $delegators);

        $fusedQsr = qsr_token()->getDisplayAmount(Fusion::isActive()->sum('amount'));
        Cache::put('fused-qsr', $fusedQsr);

        $stakedZnn = znn_token()->getDisplayAmount(Staker::isActive()->sum('amount'), 0);
        Cache::put('staked-znn', $stakedZnn);

        Artisan::call('zenon:update-node-list');

        return self::SUCCESS;
    }
}
