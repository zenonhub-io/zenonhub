<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Bridge;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Nom\Models\AccountBlock;
use Illuminate\Support\Facades\Log;

class NominateGuardians extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        if (! validate_bridge_tx($this->block)) {
            Log::warning('Bridge action sent from non-admin');

            return;
        }

    }
}