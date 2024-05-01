<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Bridge;

use App\Domains\Indexer\Actions\AbstractIndexerAction;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\BridgeUnwrap;
use Illuminate\Support\Facades\Log;
use Throwable;

class RevokeUnwrapRequest extends AbstractIndexerAction
{
    public BridgeUnwrap $unwrap;

    public function handle(AccountBlock $accountBlock): void
    {
        try {
            $this->loadUnwrap();
            $this->processRevokeUnwrap();
        } catch (Throwable $exception) {
            Log::warning('Error revoking unwrap request ' . $this->accountBlock->hash);
            Log::debug($exception);

            return;
        }

    }

    private function loadUnwrap(): void
    {
        $data = $this->accountBlock->data->decoded;
        $this->unwrap = BridgeUnwrap::where('transaction_hash', $data['transactionHash'])
            ->where('log_index', $data['logIndex'])
            ->sole();
    }

    private function processRevokeUnwrap(): void
    {
        $this->unwrap->revoked_at = $this->accountBlock->created_at;
        $this->unwrap->save();
    }
}
