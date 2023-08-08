<?php

namespace App\Bots;

use Illuminate\Notifications\Notifiable;

class WhaleAlertBot
{
    use Notifiable;

    public function routeNotificationForTwitter($notification)
    {
        return [
            config('whale-alerts.twitter.consumer_key'),
            config('whale-alerts.twitter.consumer_secret'),
            config('whale-alerts.twitter.access_token'),
            config('whale-alerts.twitter.access_token_secret'),
        ];
    }

    public function routeNotificationForTelegram()
    {
        return config('whale-alerts.telegram.chat');
    }

    public function routeNotificationForDiscordWebhook()
    {
        return config('whale-alerts.discord.webhook');
    }
}
