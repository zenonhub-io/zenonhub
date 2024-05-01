<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Sentinel;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Sentinel;
use App\Models\NotificationType;
use Illuminate\Support\Facades\Notification;

class Revoke extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $sentinel = Sentinel::where('owner_id', $this->accountBlock->account->id)->isActive()->first();

        if (! $sentinel) {
            return;
        }

        $sentinel->revoked_at = $this->accountBlock->created_at;
        $sentinel->save();

        $this->notifyUsers($sentinel);

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
