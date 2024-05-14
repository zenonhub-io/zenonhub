<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Bridge;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\BridgeAdmin;
use Illuminate\Support\Facades\Log;

class ProposeAdministrator extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        if (! validate_bridge_tx($this->block)) {
            Log::warning('Bridge action sent from non-admin');

            return;
        }

        $this->proposeAdmin();

    }

    private function proposeAdmin(): void
    {
        $proposedAccount = load_account($accountBlock->data->decoded['address']);
        BridgeAdmin::create([
            'account_id' => $proposedAccount->id,
            'nominated_at' => $accountBlock->created_at,
        ]);
    }
}
