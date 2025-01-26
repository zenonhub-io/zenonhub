<?php

declare(strict_types=1);

namespace App\Listeners\Notifications\Sentinel;

use App\Bots\NetworkAlertBot;
use App\Events\Indexer\Sentinel\SentinelRevoked;
use App\Listeners\Notifications\BaseListener;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Sentinel;
use App\Notifications\Nom\Sentinel\RevokedNotification;
use Illuminate\Support\Facades\Notification;
use Lorisleiva\Actions\Concerns\AsAction;

class SentinelRevokedListener extends BaseListener
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

    public function asListener(SentinelRevoked $sentinelRevokedEvent): void
    {
        $this->handle($sentinelRevokedEvent->accountBlock, $sentinelRevokedEvent->sentinel);
    }

    private function sendToUsers(Sentinel $sentinel): void
    {
        $this->getSubscribedUsers(self::NOTIFICATION_TYPE)
            ->chunkById(500, function ($users) use ($sentinel) {
                Notification::send($users, new RevokedNotification($sentinel));
            });
    }

    private function sendToNetworkBot(Sentinel $sentinel): void
    {
        Notification::send(new NetworkAlertBot, new RevokedNotification($sentinel));
    }
}
