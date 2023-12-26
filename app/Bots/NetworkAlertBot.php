<?php

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
}
