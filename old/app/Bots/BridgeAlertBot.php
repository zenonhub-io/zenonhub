<?php

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

    public function routeNotificationForTelegram()
    {
        return config('bots.bridge-alerts.telegram.chat');
    }

    public function routeNotificationForDiscordWebhook()
    {
        return config('bots.bridge-alerts.discord.webhook');
    }
}
