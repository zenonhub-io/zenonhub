<?php

declare(strict_types=1);

namespace App\Listeners\Notifications\Sentinel;

use App\Bots\NetworkAlertBot;
use App\Events\Indexer\Sentinel\SentinelRegistered;
use App\Listeners\Notifications\BaseListener;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Sentinel;
use App\Notifications\Nom\Sentinel\RegisteredNotification;
use Illuminate\Support\Facades\Notification;
use Lorisleiva\Actions\Concerns\AsAction;

class SentinelRegisteredListener extends BaseListener
{
    use AsAction;

    private const NOTIFICATION_TYPE = 'network-sentinel';

    /**
     * Handle the event.
     */
    public function handle(AccountBlock $accountBlock, Sentinel $sentinel): void
    {
        $this->sendToUsers($sentinel);
        $this->sendToNetworkBot($sentinel);
    }

    public function asListener(SentinelRegistered $sentinelRegisteredEvent): void
    {
        $this->handle($sentinelRegisteredEvent->accountBlock, $sentinelRegisteredEvent->sentinel);
    }

    private function sendToUsers(Sentinel $sentinel): void
    {
        $this->getSubscribedUsers(self::NOTIFICATION_TYPE)
            ->chunkById(500, function ($users) use ($sentinel) {
                Notification::send($users, new RegisteredNotification($sentinel));
            });
    }

    private function sendToNetworkBot(Sentinel $sentinel): void
    {
        Notification::send(new NetworkAlertBot, new RegisteredNotification($sentinel));
    }
}
