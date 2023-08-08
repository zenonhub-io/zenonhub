<?php

namespace App\Notifications\Nom\Pillar;

use App\Models\Nom\Pillar;
use App\Notifications\Nom\BaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class Registered extends BaseNotification implements ShouldQueue
{
    use Queueable;

    protected Pillar $pillar;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($type, $pillar)
    {
        parent::__construct($type);
        $this->pillar = $pillar;
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
            ->markdown('mail.notifications.pillar.registered', [
                'user' => $notifiable,
                'pillar' => $this->pillar,
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
