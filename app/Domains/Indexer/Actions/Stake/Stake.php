<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Stake;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Stake as StakeModel;
use Illuminate\Support\Facades\Cache;

use function App\Jobs\Nom\Stake\znn_token;

class Stake extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $this->accountBlock->data->decoded;

        StakeModel::create([
            'chain_id' => $this->accountBlock->chain->id,
            'account_id' => $this->accountBlock->account_id,
            'token_id' => $this->accountBlock->token_id,
            'amount' => $this->accountBlock->amount,
            'duration' => $blockData['durationInSec'],
            'hash' => $this->accountBlock->hash,
            'started_at' => $this->accountBlock->created_at,
        ]);

        $totalZnnStaked = StakeModel::isActive()->isZnn()->sum('amount');
        $stakedZnn = znn_token()->getFormattedAmount($totalZnnStaked, 0);
        Cache::put('staked-znn', $stakedZnn);

    }
}
