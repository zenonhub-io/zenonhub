<?php

namespace App\Bots;

use Illuminate\Notifications\Notifiable;

class NetworkAlertBot
{
    use Notifiable;

    public function routeNotificationForTwitter($notification): array
    {
        return [
            config('network-alerts.twitter.consumer_key'),
            config('network-alerts.twitter.consumer_secret'),
            config('network-alerts.twitter.access_token'),
            config('network-alerts.twitter.access_token_secret'),
        ];
    }
}
