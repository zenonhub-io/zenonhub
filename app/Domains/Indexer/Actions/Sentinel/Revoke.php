<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Sentinel;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Sentinel\SentinelRevoked;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Sentinel;
use App\Models\NotificationType;
use Illuminate\Support\Facades\Notification;

class Revoke extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $this->accountBlock = $accountBlock;
        $blockData = $accountBlock->data->decoded;

        $sentinel = Sentinel::whereOwner($accountBlock->account_id)
            ->isActive()
            ->first();

        if (! $sentinel || ! $this->validateAction($sentinel)) {
            return;
        }

        $sentinel->revoked_at = $accountBlock->created_at;
        $sentinel->save();

        SentinelRevoked::dispatch($accountBlock, $sentinel);
    }

    protected function validateAction(): bool
    {
        [$sentinel] = func_get_args();

        // check sentinel revoke window
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
