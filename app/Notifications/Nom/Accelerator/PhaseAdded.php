<?php

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

class PhaseAdded extends Notification implements ShouldQueue
{
    use Queueable;

    protected AcceleratorPhase $phase;

    public function __construct(AcceleratorPhase $phase)
    {
        $this->phase = $phase;
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
            ->subject(get_env_prefix().'New phase')
            ->markdown('mail.notifications.nom.az.phase-added', [
                'user' => $notifiable,
                'phase' => $this->phase,
            ]);
    }

    public function toTwitter($notifiable): TwitterMessage
    {
        return new TwitterStatusUpdate('Testing Twitter network alerts');
    }
}
