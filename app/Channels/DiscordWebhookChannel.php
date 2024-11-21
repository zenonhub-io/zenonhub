<?php

declare(strict_types=1);

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class DiscordWebhookChannel
{
    public function send(mixed $notifiable, Notification $notification): void
    {
        $webhookUrl = $notifiable->routeNotificationFor('discordWebhook', $notification);
        $message = $notification->toDiscordWebhook($notifiable);
        $payload = collect($message->toArray())->forget('file')->all();

        Http::post($webhookUrl, $payload);
    }
}
