<?php

namespace App\Bots;

use Illuminate\Notifications\Notifiable;

class WhaleAlertBot
{
    use Notifiable;

    public function routeNotificationForTwitter($notification): array
    {
        return [
            config('bots.whale-alerts.twitter.consumer_key'),
            config('bots.whale-alerts.twitter.consumer_secret'),
            config('bots.whale-alerts.twitter.access_token'),
            config('bots.whale-alerts.twitter.access_token_secret'),
        ];
    }

    public function routeNotificationForTelegram()
    {
        return config('bots.whale-alerts.telegram.chat');
    }

    public function routeNotificationForDiscordWebhook()
    {
        return config('bots.whale-alerts.discord.webhook');
    }
}
