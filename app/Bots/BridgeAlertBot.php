<?php

declare(strict_types=1);

namespace App\Bots;

use Illuminate\Notifications\Notifiable;

class BridgeAlertBot
{
    use Notifiable;

    public function routeNotificationForTwitter($notification): array
    {
        return [
            config('bots.bridge-alerts.twitter.consumer_key'),
            config('bots.bridge-alerts.twitter.consumer_secret'),
            config('bots.bridge-alerts.twitter.access_token'),
            config('bots.bridge-alerts.twitter.access_token_secret'),
        ];
    }

    public function routeNotificationForTelegram(): string
    {
        return config('bots.bridge-alerts.telegram.chat');
    }

    public function routeNotificationForDiscordWebhook(): string
    {
        return config('bots.bridge-alerts.discord.webhook');
    }
}
