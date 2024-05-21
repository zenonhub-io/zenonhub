<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Stake;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Stake\EndStake;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Stake;
use Illuminate\Support\Facades\Log;

class Cancel extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;
        $stake = Stake::findBy('hash', $blockData['id']);

        if (! $stake || ! $this->validateAction($accountBlock, $stake)) {
            Log::info('Contract Method Processor - Stake: Cancel failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
            ]);

            return;
        }

        $stake->ended_at = $accountBlock->created_at;
        $stake->save();

        EndStake::dispatch($accountBlock, $stake);

        Log::info('Contract Method Processor - Stake: Cancel complete', [
            'accountBlock' => $accountBlock->hash,
            'blockData' => $stake,
        ]);

        $this->setBlockAsProcessed($accountBlock);
    }

    public function validateAction(): bool
    {
        /**
         * @var AccountBlock $accountBlock
         * @var Stake $stake
         */
        [$accountBlock, $stake] = func_get_args();

        if ($stake->end_date > $accountBlock->created_at) {
            return false;
        }

        if ($stake->account_id !== $accountBlock->account_id) {
            return false;
        }

        return true;
    }
}
