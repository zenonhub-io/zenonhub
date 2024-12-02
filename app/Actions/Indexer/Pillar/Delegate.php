<?php

declare(strict_types=1);

namespace App\Actions\Indexer\Pillar;

use App\Actions\Indexer\AbstractContractMethodProcessor;
use App\Events\Indexer\Pillar\AccountDelegated;
use App\Exceptions\IndexerActionValidationException;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Pillar;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class Delegate extends AbstractContractMethodProcessor
{
    public Pillar $pillar;

    public function handle(AccountBlock $accountBlock): void
    {
        $accountBlock->load('account');
        $blockData = $accountBlock->data->decoded;
        $pillar = Pillar::firstWhere('name', $blockData['name']);

        try {
            $this->validateAction($accountBlock, $pillar);
        } catch (IndexerActionValidationException $e) {
            Log::info('Contract Method Processor - Pillar: Delegate failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        Cache::forget($pillar->cacheKey('pillar-rank', 'updated_at'));

        //        $accountBlock->account
        //            ->delegations()
        //            ->newPivotStatementForId($accountBlock->account_id)
        //            ->where('ended_at', null)
        //            ->update(['ended_at' => $accountBlock->created_at]);

        $accountBlock->account->delegations()->attach($pillar->id, [
            'started_at' => $accountBlock->created_at,
        ]);

        AccountDelegated::dispatch($accountBlock, $accountBlock->account, $pillar);

        Log::info('Contract Method Processor - Pillar: Delegate complete', [
            'accountBlock' => $accountBlock->hash,
            'blockData' => $blockData,
            'account' => $accountBlock->account->address,
            'pillar' => $pillar->name,

        ]);

        $this->setBlockAsProcessed($accountBlock);
    }

    /**
     * @throws IndexerActionValidationException
     */
    public function validateAction(): void
    {
        /**
         * @var AccountBlock $accountBlock
         * @var Pillar $pillar
         */
        [$accountBlock, $pillar] = func_get_args();

        if (! $pillar) {
            throw new IndexerActionValidationException('Invalid pillar');
        }

        if ($pillar->revoked_at !== null) {
            throw new IndexerActionValidationException('Pillar is revoked');
        }
    }

    //    private function notifyUsers(): void
    //    {
    //        $subscribedUsers = User::whereHas('notification_types', fn ($query) => $query->where('code', 'pillar-delegator-added'))
    //            ->whereHas('nom_accounts', function ($query) {
    //                $query->whereHas('pillars', fn ($query) => $query->where('id', $this->pillar->id));
    //            })
    //            ->get();
    //
    //        Notification::send(
    //            $subscribedUsers,
    //            new \App\Notifications\Nom\Pillar\NewDelegator($this->pillar, $accountBlock->account)
    //        );
    //    }
}
