<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Plasma;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Plasma\StartFuse;
use App\Domains\Nom\Enums\NetworkTokensEnum;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Plasma;
use Illuminate\Support\Facades\Log;

class Fuse extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $accountBlock->load('token');
        $blockData = $accountBlock->data->decoded;

        if (! $this->validateAction($accountBlock)) {
            Log::info('Contract Method Processor - Plasma: Fuse failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
            ]);

            return;
        }

        $plasma = Plasma::create([
            'chain_id' => $accountBlock->chain_id,
            'from_account_id' => $accountBlock->account_id,
            'to_account_id' => load_account($blockData['address'])->id,
            'account_block_id' => $accountBlock->id,
            'amount' => $accountBlock->amount,
            'hash' => $accountBlock->hash,
            'started_at' => $accountBlock->created_at,
        ]);

        StartFuse::dispatch($accountBlock, $plasma);

        Log::info('Contract Method Processor - Plasma: Fuse complete', [
            'accountBlock' => $accountBlock->hash,
            'blockData' => $blockData,
            'plasma' => $plasma,
        ]);

        // TODO - refactor event into new listener
        //\App\Events\Nom\Plasma\Fuse::dispatch($accountBlock, $blockData);

        $this->setBlockAsProcessed($accountBlock);
    }

    public function validateAction(): bool
    {
        /**
         * @var AccountBlock $accountBlock
         */
        [$accountBlock] = func_get_args();

        if ($accountBlock->token->token_standard !== NetworkTokensEnum::QSR->value) {
            return false;
        }

        if ($accountBlock->amount < config('nom.plasma.minAmount')) {
            return false;
        }

        // make sure users send multiple of constants.CostPerFusionUnit
        if (bcmod($accountBlock->amount, config('nom.plasma.costPerFusionUnit')) !== '0') {
            return false;
        }

        return true;
    }
}
