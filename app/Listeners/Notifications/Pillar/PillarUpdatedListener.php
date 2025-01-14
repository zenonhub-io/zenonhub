<?php

declare(strict_types=1);

namespace App\Listeners\Notifications\Pillar;

use App\Bots\NetworkAlertBot;
use App\Events\Indexer\Pillar\PillarUpdated;
use App\Listeners\Notifications\BaseListener;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Pillar;
use App\Notifications\Nom\Pillar\UpdatedNotification;
use Illuminate\Support\Facades\Notification;
use Lorisleiva\Actions\Concerns\AsAction;

class PillarUpdatedListener extends BaseListener
{
    use AsAction;

    private const NOTIFICATION_TYPE = 'network-pillar';

    /**
     * Handle the event.
     */
    public function handle(AccountBlock $accountBlock, Pillar $pillar): void
    {
        $this->sendToUsers($pillar);
        $this->sendToNetworkBot($pillar);
    }

    public function asListener(PillarUpdated $pillarUpdatedEvent): void
    {
        $this->handle($pillarUpdatedEvent->accountBlock, $pillarUpdatedEvent->pillar);
    }

    private function sendToUsers(Pillar $pillar): void
    {
        $this->getSubscribedUsers(self::NOTIFICATION_TYPE)
            ->chunkById(500, function ($users) use ($pillar) {
                Notification::send($users, new UpdatedNotification($pillar));
            });
    }

    private function sendToNetworkBot(Pillar $pillar): void
    {
        Notification::send(new NetworkAlertBot, new UpdatedNotification($pillar));
    }
}
