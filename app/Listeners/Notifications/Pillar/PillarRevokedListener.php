<?php

declare(strict_types=1);

namespace App\Listeners\Notifications\Pillar;

use App\Bots\NetworkAlertBot;
use App\Events\Indexer\Pillar\PillarRevoked;
use App\Listeners\Notifications\BaseListener;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Pillar;
use App\Notifications\Nom\Pillar\RevokedNotification;
use Illuminate\Support\Facades\Notification;
use Lorisleiva\Actions\Concerns\AsAction;

class PillarRevokedListener extends BaseListener
{
    use AsAction;

    private const string NOTIFICATION_TYPE = 'network-pillar';

    /**
     * Handle the event.
     */
    public function handle(AccountBlock $accountBlock, Pillar $pillar): void
    {
        $this->sendToUsers($pillar);
        $this->sendToNetworkBot($pillar);
    }

    public function asListener(PillarRevoked $pillarRevokedEvent): void
    {
        $this->handle($pillarRevokedEvent->accountBlock, $pillarRevokedEvent->pillar);
    }

    private function sendToUsers(Pillar $pillar): void
    {
        $this->getSubscribedUsers(self::NOTIFICATION_TYPE)
            ->chunkById(500, function ($users) use ($pillar) {
                Notification::send($users, new RevokedNotification($pillar));
            });
    }

    private function sendToNetworkBot(Pillar $pillar): void
    {
        Notification::send(new NetworkAlertBot, new RevokedNotification($pillar));
    }
}
