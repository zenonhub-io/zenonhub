<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Pillar;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Pillar\PillarRevoked;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Pillar;
use App\Models\NotificationType;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class Revoke extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;
        $pillar = Pillar::findBy('name', $blockData['name']);

        if (! $pillar || ! $this->validateAction($accountBlock, $pillar)) {
            Log::info('Contract Method Processor - Pillar: Revoke failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
            ]);

            return;
        }

        $pillar->weight = 0;
        $pillar->produced_momentums = 0;
        $pillar->expected_momentums = 0;
        $pillar->missed_momentums = 0;
        $pillar->revoked_at = $accountBlock->created_at;
        $pillar->save();

        PillarRevoked::dispatch($accountBlock, $pillar);

        Log::info('Contract Method Processor - Pillar: Revoke complete', [
            'accountBlock' => $accountBlock->hash,
            'blockData' => $blockData,
            'pillar' => $pillar,
        ]);

        $this->setBlockAsProcessed($accountBlock);
    }

    public function validateAction(): bool
    {
        /**
         * @var AccountBlock $accountBlock
         * @var Pillar $pillar
         */
        [$accountBlock, $pillar] = func_get_args();

        if ($pillar->revoked_at !== null) {
            return false;
        }

        if ($pillar->owner_id !== $accountBlock->account_id) {
            return false;
        }

        if (! $pillar->getIsRevokableAttribute($accountBlock->created_at)) {
            return false;
        }

        return true;
    }

    private function notifyUsers($pillar): void
    {
        $subscribedUsers = NotificationType::getSubscribedUsers('network-pillar');
        $networkBot = new \App\Bots\NetworkAlertBot;

        Notification::send(
            $subscribedUsers->prepend($networkBot),
            new \App\Notifications\Nom\Pillar\Revoked($pillar)
        );
    }
}
