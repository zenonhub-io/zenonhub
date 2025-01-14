<?php

declare(strict_types=1);

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

class UpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Pillar $pillar;

    public function __construct(Pillar $pillar)
    {
        $this->onQueue('notifications');
        $this->pillar = $pillar;
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
            ->subject(get_env_prefix() . 'Pillar updated')
            ->markdown('mail.notifications.nom.pillar.updated', [
                'user' => $notifiable,
                'pillar' => $this->pillar,
                'link' => $this->getItemLink(),
            ]);
    }

    public function toTwitter($notifiable): TwitterMessage
    {
        $link = $this->getItemLink('twitter');

        return new TwitterStatusUpdate("ℹ️ A pillar has been updated! {$this->pillar->name} changed their rewards to {$this->pillar->momentum_rewards }% / {$this->pillar->delegate_rewards}%

🔗 $link");
    }

    private function getItemLink(string $source = 'email'): string
    {
        return route('pillar.detail', [
            'slug' => $this->pillar->slug,
            'utm_source' => 'network_bot',
            'utm_medium' => $source,
        ]);
    }
}
