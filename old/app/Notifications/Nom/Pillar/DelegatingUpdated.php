<?php

declare(strict_types=1);

namespace App\Notifications\Nom\Pillar;

use App\Models\Nom\Pillar;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DelegatingUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected Pillar $pillar;

    public function __construct(Pillar $pillar)
    {
        $this->pillar = $pillar;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $accounts = $notifiable->nom_accounts->map(function ($account) {
            if ($account->active_delegation && $account->active_delegation->pillar->id === $this->pillar->id) {
                return $account;
            }

            return null;
        })->filter();

        return (new MailMessage)
            ->subject(get_env_prefix() . 'Delegating pillar updated')
            ->markdown('mail.notifications.pillar.delegating-updated', [
                'user' => $notifiable,
                'pillar' => $this->pillar,
                'accounts' => $accounts,
            ]);
    }
}
