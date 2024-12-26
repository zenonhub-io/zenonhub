<?php

declare(strict_types=1);

namespace App\Actions\Indexer\Pillar;

use App\Actions\Indexer\AbstractContractMethodProcessor;
use App\Events\Indexer\Pillar\PillarRevoked;
use App\Exceptions\IndexerActionValidationException;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Pillar;
use App\Models\NotificationType;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class Revoke extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;
        $pillar = Pillar::firstWhere('name', $blockData['name']);

        try {
            $this->validateAction($accountBlock, $pillar);
        } catch (IndexerActionValidationException $e) {
            Log::error('Contract Method Processor - Pillar: Revoke failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
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
            throw new IndexerActionValidationException('Pillar already revoked');
        }

        if ($pillar->owner_id !== $accountBlock->account_id) {
            throw new IndexerActionValidationException('Account is not pillar owner');
        }

        if (! $pillar->getIsRevokableAttribute($accountBlock->created_at)) {
            throw new IndexerActionValidationException('Pillar not currently revocable');
        }
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
