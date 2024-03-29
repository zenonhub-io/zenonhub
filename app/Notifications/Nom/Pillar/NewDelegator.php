<?php

namespace App\Notifications\Nom\Pillar;

use App\Models\Nom\Account;
use App\Models\Nom\Pillar;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewDelegator extends Notification implements ShouldQueue
{
    use Queueable;

    protected Pillar $pillar;

    protected Account $account;

    public function __construct(Pillar $pillar, Account $account)
    {
        $this->pillar = $pillar;
        $this->account = $account;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(get_env_prefix().'New delegator')
            ->markdown('mail.notifications.pillar.new-delegator', [
                'user' => $notifiable,
                'pillar' => $this->pillar,
                'account' => $this->account,
            ]);
    }
}
