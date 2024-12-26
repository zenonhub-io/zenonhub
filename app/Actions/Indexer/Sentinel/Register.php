<?php

declare(strict_types=1);

namespace App\Actions\Indexer\Sentinel;

use App\Actions\Indexer\AbstractContractMethodProcessor;
use App\Enums\Nom\NetworkTokensEnum;
use App\Events\Indexer\Sentinel\SentinelRegistered;
use App\Exceptions\IndexerActionValidationException;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Sentinel;
use App\Models\NotificationType;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class Register extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $accountBlock->load('token');
        $blockData = $accountBlock->data->decoded;

        try {
            $this->validateAction($accountBlock);
        } catch (IndexerActionValidationException $e) {
            Log::error('Contract Method Processor - Sentinel: Register failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        $sentinel = Sentinel::create([
            'chain_id' => $accountBlock->chain_id,
            'owner_id' => $accountBlock->account_id,
            'created_at' => $accountBlock->created_at,
        ]);

        SentinelRegistered::dispatch($accountBlock, $sentinel);

        Log::error('Contract Method Processor - Sentinel: Register complete', [
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
         */
        [$accountBlock] = func_get_args();

        if ($accountBlock->token->token_standard !== NetworkTokensEnum::ZNN->value) {
            throw new IndexerActionValidationException('Invalid token');
        }

        if ($accountBlock->amount !== config('nom.sentinel.znnRegisterAmount')) {
            throw new IndexerActionValidationException('Amount doesnt match sentinel registration cost');
        }
    }

    private function notifyUsers($sentinel): void
    {
        $subscribedUsers = NotificationType::getSubscribedUsers('network-sentinel');
        $networkBot = new \App\Bots\NetworkAlertBot;

        Notification::send(
            $subscribedUsers->prepend($networkBot),
            new \App\Notifications\Nom\Sentinel\Registered($sentinel)
        );
    }
}
