<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Bridge;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\BridgeUnwrap;
use Illuminate\Support\Facades\Log;
use Throwable;

class RevokeUnwrapRequest extends AbstractContractMethodProcessor
{
    public BridgeUnwrap $unwrap;

    public function handle(AccountBlock $accountBlock): void
    {
        $this->accountBlock = $accountBlock;
        $blockData = $accountBlock->data->decoded;

        try {
            $this->loadUnwrap();
            $this->processRevokeUnwrap();
        } catch (Throwable $exception) {
            Log::warning('Error revoking unwrap request ' . $accountBlock->hash);
            Log::debug($exception);

            return;
        }

    }

    private function loadUnwrap(): void
    {
        $data = $accountBlock->data->decoded;
        $this->unwrap = BridgeUnwrap::where('transaction_hash', $data['transactionHash'])
            ->where('log_index', $data['logIndex'])
            ->sole();
    }

    private function processRevokeUnwrap(): void
    {
        $this->unwrap->revoked_at = $accountBlock->created_at;
        $this->unwrap->save();
    }
}
