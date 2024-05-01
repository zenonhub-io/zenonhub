<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Plasma;

use App\Domains\Indexer\Actions\AbstractIndexerAction;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Plasma;
use Illuminate\Support\Facades\Cache;

use function App\Jobs\Nom\Plasma\qsr_token;

class Fuse extends AbstractIndexerAction
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $this->accountBlock->data->decoded;
        $toAccount = load_account($blockData['address']);

        Plasma::create([
            'chain_id' => $this->accountBlock->chain->id,
            'from_account_id' => $this->accountBlock->account_id,
            'to_account_id' => $toAccount->id,
            'amount' => $this->accountBlock->amount,
            'hash' => $this->accountBlock->hash,
            'started_at' => $this->accountBlock->created_at,
            'ended_at' => null,
        ]);

        $fusedQsr = qsr_token()->getFormattedAmount(Plasma::isActive()->sum('amount'), 0);
        Cache::put('fused-qsr', $fusedQsr);

        \App\Events\Nom\Plasma\Fuse::dispatch($this->block, $blockData);
    }
}
