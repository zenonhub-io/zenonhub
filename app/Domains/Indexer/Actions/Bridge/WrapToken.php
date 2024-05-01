<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Bridge;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\BridgeNetwork;
use App\Domains\Nom\Models\BridgeWrap;
use Illuminate\Support\Facades\Log;
use Throwable;

class WrapToken extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        try {
            $this->processWrap();
        } catch (Throwable $exception) {
            Log::warning('Unable to process wrap: ' . $this->accountBlock->hash);
            Log::debug($exception);

            return;
        }

    }

    private function processWrap(): void
    {
        $data = $this->accountBlock->data->decoded;
        $network = BridgeNetwork::findByNetworkChain($data['networkClass'], $data['chainId']);

        BridgeWrap::create([
            'bridge_network_id' => $network->id,
            'account_id' => $this->accountBlock->account_id,
            'token_id' => $this->accountBlock->token_id,
            'account_block_id' => $this->accountBlock->id,
            'to_address' => $data['toAddress'],
            'amount' => $this->accountBlock->amount,
            'created_at' => $this->accountBlock->created_at,
        ]);
    }
}
