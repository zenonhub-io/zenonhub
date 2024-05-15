<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Sentinel;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Sentinel\SentinelRegistered;
use App\Domains\Nom\Enums\NetworkTokensEnum;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Sentinel;
use App\Models\NotificationType;
use Illuminate\Support\Facades\Notification;

class Register extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $this->accountBlock = $accountBlock;

        if (! $this->validateAction()) {
            return;
        }

        $sentinel = Sentinel::create([
            'chain_id' => $accountBlock->chain_id,
            'owner_id' => $accountBlock->account_id,
            'created_at' => $accountBlock->created_at,
        ]);

        SentinelRegistered::dispatch($accountBlock, $sentinel);
    }

    protected function validateAction(): bool
    {
        if ($this->accountBlock->token->token_standard !== NetworkTokensEnum::ZNN->value) {
            return false;
        }

        return $this->accountBlock->amount === 500000000000;
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
