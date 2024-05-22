<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Sentinel;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Sentinel\SentinelRevoked;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Sentinel;
use App\Models\NotificationType;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class Revoke extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data?->decoded;
        $sentinel = Sentinel::whereOwner($accountBlock->account_id)->isActive()->first();

        if (! $sentinel || ! $this->validateAction($accountBlock, $sentinel)) {
            Log::info('Contract Method Processor - Sentinel: Revoke failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
            ]);

            return;
        }

        $sentinel->revoked_at = $accountBlock->created_at;
        $sentinel->save();

        SentinelRevoked::dispatch($accountBlock, $sentinel);

        Log::info('Contract Method Processor - Sentinel: Revoke complete', [
            'accountBlock' => $accountBlock->hash,
            'blockData' => $blockData,
            'sentinel' => $sentinel,
        ]);

        $this->setBlockAsProcessed($accountBlock);
    }

    public function validateAction(): bool
    {
        /**
         * @var AccountBlock $accountBlock
         * @var Sentinel $sentinel
         */
        [$accountBlock, $sentinel] = func_get_args();

        if ($sentinel->revoked_at !== null) {
            return false;
        }

        if (! $sentinel->getIsRevokableAttribute($accountBlock->created_at)) {
            return false;
        }

        return true;
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
