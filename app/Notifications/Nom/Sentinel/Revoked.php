<?php

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

class Revoked extends Notification implements ShouldQueue
{
    use Queueable;

    protected Sentinel $sentinel;

    public function __construct(Sentinel $sentinel)
    {
        $this->sentinel = $sentinel;
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
            ->subject(get_env_prefix().'Sentinel revoked')
            ->markdown('mail.notifications.nom.sentinel.revoked', [
                'user' => $notifiable,
                'sentinel' => $this->sentinel,
            ]);
    }

    public function toTwitter($notifiable): TwitterMessage
    {
        $link = route('explorer.account', [
            'address' => $this->sentinel->owner->address,
            'utm_source' => 'network_bot',
            'utm_medium' => 'twitter',
        ]);

        return new TwitterStatusUpdate("ℹ️ A sentinel has been revoked!

🔗 $link

#ZenonNetworkAlert #Zenon");
    }
}
