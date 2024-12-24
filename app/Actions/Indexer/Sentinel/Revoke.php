<?php

declare(strict_types=1);

namespace App\Actions\Indexer\Sentinel;

use App\Actions\Indexer\AbstractContractMethodProcessor;
use App\Events\Indexer\Sentinel\SentinelRevoked;
use App\Exceptions\IndexerActionValidationException;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Sentinel;
use App\Models\NotificationType;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class Revoke extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;
        $sentinel = Sentinel::whereOwner($accountBlock->account_id)->whereActive()->first();

        try {
            $this->validateAction($accountBlock, $sentinel);
        } catch (IndexerActionValidationException $e) {
            Log::error('Contract Method Processor - Sentinel: Revoke failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        $sentinel->revoked_at = $accountBlock->created_at;
        $sentinel->save();

        SentinelRevoked::dispatch($accountBlock, $sentinel);

        Log::error('Contract Method Processor - Sentinel: Revoke complete', [
            'accountBlock' => $accountBlock->hash,
            'blockData' => $blockData,
            'sentinel' => $sentinel,
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
         * @var Sentinel $sentinel
         */
        [$accountBlock, $sentinel] = func_get_args();

        if (! $sentinel) {
            throw new IndexerActionValidationException('Invalid sentinel');
        }

        if (! $sentinel->getIsRevokableAttribute($accountBlock->created_at)) {
            throw new IndexerActionValidationException('Sentinel not revocable');
        }
    }

    private function notifyUsers($sentinel): void
    {
        $subscribedUsers = NotificationType::getSubscribedUsers('network-sentinel');
        $networkBot = new \App\Bots\NetworkAlertBot;

        Notification::send(
            $subscribedUsers->prepend($networkBot),
            new \App\Notifications\Nom\Sentinel\Revoked($sentinel)
        );
    }
}
