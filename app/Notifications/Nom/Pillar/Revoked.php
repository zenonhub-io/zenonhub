<?php

namespace App\Notifications\Nom\Pillar;

use App\Bots\NetworkAlertBot;
use App\Models\Nom\Pillar;
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

    protected Pillar $pillar;

    public function __construct(Pillar $pillar)
    {
        $this->pillar = $pillar;
    }

    public function via($notifiable): array
    {
        $channels = [];

        if ($notifiable instanceof NetworkAlertBot) {
            if (config('network-alerts.twitter.enabled')) {
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
            ->subject(get_env_prefix().'Pillar revoked')
            ->markdown('mail.notifications.nom.pillar.revoked', [
                'user' => $notifiable,
                'pillar' => $this->pillar,
            ]);
    }

    public function toTwitter($notifiable): TwitterMessage
    {
        $accountName = short_address($this->pillar->owner);
        $link = route('pillars.detail', [
            'slug' => $this->pillar->slug,
            'utm_source' => 'network_bot',
            'utm_medium' => 'twitter',
        ]);

        return new TwitterStatusUpdate("ℹ️ A pillar has been revoked! {$this->pillar->name} was dismantled by {$accountName}

🔗 $link

#ZenonNetworkAlert #Zenon");
    }
}
