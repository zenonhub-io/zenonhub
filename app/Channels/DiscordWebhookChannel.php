<?php

namespace App\Channels;

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

        return App::make('discord.api', [
            'webhook' => $webhook,
        ])->send($message);
    }
}
