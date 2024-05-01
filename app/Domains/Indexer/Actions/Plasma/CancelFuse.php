<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Plasma;

use App\Domains\Indexer\Actions\AbstractIndexerAction;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Plasma;
use Illuminate\Support\Facades\Cache;

use function App\Jobs\Nom\Plasma\qsr_token;

class CancelFuse extends AbstractIndexerAction
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $this->accountBlock->data->decoded;
        $fusion = Plasma::whereHash($blockData['id'])->first();

        if ($fusion) {
            $fusion->ended_at = $this->accountBlock->created_at;
            $fusion->save();
        }

        $fusedQsr = qsr_token()->getFormattedAmount(Plasma::isActive()->sum('amount'), 0);
        Cache::put('fused-qsr', $fusedQsr);

        \App\Events\Nom\Plasma\CancelFuse::dispatch($this->block, $blockData);
    }
}
