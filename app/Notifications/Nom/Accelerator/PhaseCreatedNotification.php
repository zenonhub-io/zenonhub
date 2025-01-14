<?php

declare(strict_types=1);

namespace App\Notifications\Nom\Accelerator;

use App\Bots\NetworkAlertBot;
use App\Models\Nom\AcceleratorPhase;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twitter\TwitterChannel;
use NotificationChannels\Twitter\TwitterMessage;
use NotificationChannels\Twitter\TwitterStatusUpdate;

class PhaseCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected AcceleratorPhase $phase;

    public function __construct(AcceleratorPhase $phase)
    {
        $this->onQueue('notifications');
        $this->phase = $phase->loadMissing('project');
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
            ->subject(get_env_prefix() . 'New phase')
            ->markdown('mail.notifications.nom.accelerator.phase-created', [
                'user' => $notifiable,
                'phase' => $this->phase,
                'link' => $this->getItemLink(),
            ]);
    }

    public function toTwitter($notifiable): TwitterMessage
    {
        $link = $this->getItemLink('twitter');

        return new TwitterStatusUpdate("ℹ️ A new phase has been created! {$this->phase->name} was added to the {$this->phase->project->name} project

🔗 $link");
    }

    private function getItemLink(string $source = 'email'): string
    {
        return route('accelerator-z.phase.detail', [
            'hash' => $this->phase->hash,
            'utm_source' => 'network_bot',
            'utm_medium' => $source,
        ]);
    }
}
