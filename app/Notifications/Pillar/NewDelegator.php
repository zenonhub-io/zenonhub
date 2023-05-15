<?php

namespace App\Notifications\Pillar;

use App\Models\Nom\Account;
use App\Models\Nom\Pillar;
use App\Notifications\BaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewDelegator extends BaseNotification implements ShouldQueue
{
    use Queueable;

    protected Pillar $pillar;

    protected Account $account;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($type, $pillar, $account)
    {
        parent::__construct($type);
        $this->pillar = $pillar;
        $this->account = $account;
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
            ->markdown('mail.notifications.pillar.new-delegator', [
                'user' => $notifiable,
                'pillar' => $this->pillar,
                'account' => $this->account,
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
