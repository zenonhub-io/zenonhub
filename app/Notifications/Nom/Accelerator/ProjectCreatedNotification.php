<?php

declare(strict_types=1);

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

class ProjectCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected AcceleratorProject $project;

    public function __construct(AcceleratorProject $project)
    {
        $this->onQueue('notifications');
        $this->project = $project;
    }

    public function via($notifiable): array
    {
        $channels = [];

        if (($notifiable instanceof NetworkAlertBot) && config('bots.network-alerts.twitter.enabled')) {
            $channels[] = TwitterChannel::class;
        }

        if ($notifiable instanceof User) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(get_env_prefix() . 'New project')
            ->markdown('mail.notifications.nom.accelerator.project-created', [
                'user' => $notifiable,
                'project' => $this->project,
            ]);
    }

    public function toTwitter($notifiable): TwitterMessage
    {
        $accountName = short_address($this->project->owner);
        $link = route('az.project', [
            'hash' => $this->project->hash,
            'utm_source' => 'network_bot',
            'utm_medium' => 'twitter',
        ]);

        return new TwitterStatusUpdate("ℹ️ A new Accelerator-Z project has been submitted! {$this->project->name} was created by {$accountName}

🔗 $link");
    }
}
