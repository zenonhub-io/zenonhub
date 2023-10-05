<?php

namespace App\Notifications\Bots;

use App\Models\Nom\AccountBlock;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twitter\TwitterChannel;
use NotificationChannels\Twitter\TwitterMessage;
use NotificationChannels\Twitter\TwitterStatusUpdate;

class NetworkAlert extends Notification
{
    use Queueable;

    public function __construct(protected AccountBlock $block)
    {
    }

    public function via($notifiable): array
    {
        $channels = [];

        if (config('network-alerts.twitter.enabled')) {
            $channels[] = TwitterChannel::class;
        }

        return $channels;
    }

    public function toTwitter($notifiable): TwitterMessage
    {
        return new TwitterStatusUpdate('Testing Twitter network alerts');
    }

    //
    // Helpers

}
