<?php

namespace App\Notifications\Nom\Accelerator;

use App\Models\Nom\AcceleratorPhase;
use App\Notifications\Nom\BaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PhaseAdded extends BaseNotification implements ShouldQueue
{
    use Queueable;

    protected AcceleratorPhase $phase;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($type, $project)
    {
        parent::__construct($type);
        $this->phase = $project;
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
            ->markdown('mail.notifications.az.phase-added', [
                'user' => $notifiable,
                'phase' => $this->phase,
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
