<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Pillar;

use App\Domains\Indexer\Actions\AbstractIndexerAction;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Pillar;
use App\Models\NotificationType;
use Illuminate\Support\Facades\Notification;

class Revoke extends AbstractIndexerAction
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $this->accountBlock->data->decoded;
        $pillar = Pillar::where('name', $blockData['name'])->first();

        if (! $pillar) {
            return;
        }

        $pillar->weight = 0;
        $pillar->produced_momentums = 0;
        $pillar->expected_momentums = 0;
        $pillar->missed_momentums = 0;
        $pillar->revoked_at = $this->accountBlock->momentum->created_at;
        $pillar->save();

        $this->notifyUsers($pillar);

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
