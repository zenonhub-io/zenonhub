<?php

namespace App\Channels;

use App\Services\Discord\DiscordWebHook;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;
use NotificationChannels\Discord\DiscordChannel as BaseChannel;

class DiscordWebhookChannel extends BaseChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $webhook = $notifiable->routeNotificationFor('discordWebhook', $notification);
        $message = $notification->toDiscordWebhook($notifiable);

        App::make(DiscordWebHook::class, [
            'webhook' => $webhook,
        ])->send($message);
    }
}
