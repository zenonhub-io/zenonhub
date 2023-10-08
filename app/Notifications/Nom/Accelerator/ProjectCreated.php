<?php

namespace App\Notifications\Nom\Accelerator;

use App\Bots\NetworkAlertBot;
use App\Models\Nom\AcceleratorProject;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twitter\TwitterChannel;
use NotificationChannels\Twitter\TwitterMessage;
use NotificationChannels\Twitter\TwitterStatusUpdate;

class ProjectCreated extends Notification implements ShouldQueue
{
    use Queueable;

    protected AcceleratorProject $project;

    public function __construct(AcceleratorProject $project)
    {
        $this->project = $project;
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
            ->subject(get_env_prefix().'New project')
            ->markdown('mail.notifications.nom.az.project-created', [
                'user' => $notifiable,
                'project' => $this->project,
            ]);
    }

    public function toTwitter($notifiable): TwitterMessage
    {
        return new TwitterStatusUpdate('Testing Twitter network alerts');
    }
}
