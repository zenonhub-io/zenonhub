<?php

namespace App\Bots;

use Illuminate\Notifications\Notifiable;

class BridgeAlertBot
{
    use Notifiable;

    public function routeNotificationForTwitter($notification): array
    {
        return [
            config('bridge-alerts.twitter.consumer_key'),
            config('bridge-alerts.twitter.consumer_secret'),
            config('bridge-alerts.twitter.access_token'),
            config('bridge-alerts.twitter.access_token_secret'),
        ];
    }

    public function routeNotificationForTelegram()
    {
        return config('bridge-alerts.telegram.chat');
    }

    public function routeNotificationForDiscordWebhook()
    {
        return config('bridge-alerts.discord.webhook');
    }
}
