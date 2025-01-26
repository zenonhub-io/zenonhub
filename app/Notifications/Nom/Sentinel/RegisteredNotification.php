<?php

declare(strict_types=1);

namespace App\Notifications\Nom\Sentinel;

use App\Bots\NetworkAlertBot;
use App\Models\Nom\Sentinel;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twitter\TwitterChannel;
use NotificationChannels\Twitter\TwitterMessage;
use NotificationChannels\Twitter\TwitterStatusUpdate;

class RegisteredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Sentinel $sentinel;

    public function __construct(Sentinel $sentinel)
    {
        $this->onQueue('notifications');
        $this->sentinel = $sentinel->loadMissing('owner');
    }

    public function via($notifiable): array
    {
        $channels = [];

        if ($notifiable instanceof NetworkAlertBot) {
            if (config('bots.network-alerts.twitter.enabled')) {
                $channels[] = TwitterChannel::class;
            }
        }

        if ($notifiable instanceof User) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(get_env_prefix() . 'New sentinel')
            ->markdown('mail.notifications.nom.sentinel.registered', [
                'user' => $notifiable,
                'sentinel' => $this->sentinel,
                'link' => $this->getItemLink(),
            ]);
    }

    public function toTwitter($notifiable): TwitterMessage
    {
        $link = $this->getItemLink('twitter');

        return new TwitterStatusUpdate("ℹ️ A new sentinel has been registered!

🔗 $link");
    }

    private function getItemLink(string $source = 'email'): string
    {
        return route('explorer.account.detail', [
            'address' => $this->sentinel->owner->address,
            'utm_source' => 'network_bot',
            'utm_medium' => $source,
        ]);
    }
}
