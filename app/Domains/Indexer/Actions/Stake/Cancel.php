<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Stake;

use App\Domains\Indexer\Actions\AbstractIndexerAction;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Stake;
use Illuminate\Support\Facades\Cache;

use function App\Jobs\Nom\Stake\znn_token;

class Cancel extends AbstractIndexerAction
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $this->accountBlock->data->decoded;

        $stake = Stake::where('hash', $blockData['id'])->first();

        if ($stake) {
            $stake->ended_at = $this->accountBlock->created_at;
            $stake->save();
        }

        $totalZnnStaked = Stake::isActive()->isZnn()->sum('amount');
        $stakedZnn = znn_token()->getFormattedAmount($totalZnnStaked, 0);
        Cache::put('staked-znn', $stakedZnn);

    }
}
