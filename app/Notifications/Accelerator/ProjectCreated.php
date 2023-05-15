<?php

namespace App\Notifications\Accelerator;

use App\Models\Nom\AcceleratorProject;
use App\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ProjectCreated extends BaseNotification implements ShouldQueue
{
    use Queueable;

    protected AcceleratorProject $project;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($type, $project)
    {
        parent::__construct($type);
        $this->project = $project;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(get_env_prefix().$this->type->name)
            ->markdown('mail.notifications.az.project-created', [
                'user' => $notifiable,
                'project' => $this->project,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
