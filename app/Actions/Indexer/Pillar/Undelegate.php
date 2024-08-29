<?php

declare(strict_types=1);

namespace App\Actions\Indexer\Pillar;

use App\Actions\Indexer\AbstractContractMethodProcessor;
use App\Events\Indexer\Pillar\AccountUndelegated;
use App\Exceptions\IndexerActionValidationException;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Pillar;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class Undelegate extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $accountBlock->load('account');
        $blockData = $accountBlock->data->decoded;

        $delegation = $accountBlock->account
            ->delegations()
            ->wherePivotNull('ended_at')
            ->first();

        try {
            $this->validateAction($accountBlock, $delegation);
        } catch (IndexerActionValidationException $e) {
            Log::info('Contract Method Processor - Pillar: Undelegate failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        Cache::forget($delegation->cacheKey('pillar-rank'));

        $accountBlock->account
            ->delegations()
            ->updateExistingPivot($delegation->id, [
                'ended_at' => $accountBlock->created_at,
            ]);

        AccountUndelegated::dispatch($accountBlock, $accountBlock->account, $delegation);

        Log::info('Contract Method Processor - Pillar: Undelegate complete', [
            'accountBlock' => $accountBlock->hash,
            'blockData' => $blockData,
            'account' => $accountBlock->account->address,
            'pillar' => $delegation->name,
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
         * @var Pillar $delegation
         */
        [$accountBlock, $delegation] = func_get_args();

        if (! $delegation) {
            throw new IndexerActionValidationException('Delegating pillar not found');
        }
    }

    private function notifyUsers($pillar): void
    {
        $subscribedUsers = User::whereHas('notification_types', fn ($query) => $query->where('code', 'pillar-delegator-lost'))
            ->whereHas('nom_accounts', function ($query) use ($pillar) {
                $query->whereHas('pillars', fn ($query) => $query->where('id', $pillar->id));
            })
            ->get();

        Notification::send(
            $subscribedUsers,
            new \App\Notifications\Nom\Pillar\LostDelegator($pillar, $accountBlock->account)
        );
    }
}
