<?php

declare(strict_types=1);

namespace App\Bots;

use Illuminate\Notifications\Notifiable;

class NetworkAlertBot
{
    use Notifiable;

    public function routeNotificationForTwitter($notification): array
    {
        return [
            config('bots.network-alerts.twitter.consumer_key'),
            config('bots.network-alerts.twitter.consumer_secret'),
            config('bots.network-alerts.twitter.access_token'),
            config('bots.network-alerts.twitter.access_token_secret'),
        ];
    }

    public function routeNotificationForTelegram(): string
    {
        return config('bots.network-alerts.telegram.chat');
    }

    public function routeNotificationForDiscordWebhook(): string
    {
        return config('bots.network-alerts.discord.webhook');
    }
}
