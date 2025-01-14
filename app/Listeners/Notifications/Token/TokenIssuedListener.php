<?php

declare(strict_types=1);

namespace App\Listeners\Notifications\Token;

use App\Bots\NetworkAlertBot;
use App\Events\Indexer\Token\TokenIssued;
use App\Listeners\Notifications\BaseListener;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Token;
use App\Notifications\Nom\Token\IssuedNotification;
use Illuminate\Support\Facades\Notification;
use Lorisleiva\Actions\Concerns\AsAction;

class TokenIssuedListener extends BaseListener
{
    use AsAction;

    private const NOTIFICATION_TYPE = 'network-token';

    /**
     * Handle the event.
     */
    public function handle(AccountBlock $accountBlock, Token $token): void
    {
        $this->sendToUsers($token);
        $this->sendToNetworkBot($token);
    }

    public function asListener(TokenIssued $tokenIssuedEvent): void
    {
        $this->handle($tokenIssuedEvent->accountBlock, $tokenIssuedEvent->token);
    }

    private function sendToUsers(Token $token): void
    {
        $this->getSubscribedUsers(self::NOTIFICATION_TYPE)
            ->chunkById(500, function ($users) use ($token) {
                Notification::send($users, new IssuedNotification($token));
            });
    }

    private function sendToNetworkBot(Token $token): void
    {
        Notification::send(new NetworkAlertBot, new IssuedNotification($token));
    }
}
