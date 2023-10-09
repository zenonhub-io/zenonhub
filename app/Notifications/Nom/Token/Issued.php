<?php

namespace App\Notifications\Nom\Token;

use App\Bots\NetworkAlertBot;
use App\Models\Nom\Account;
use App\Models\Nom\Token;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twitter\TwitterChannel;
use NotificationChannels\Twitter\TwitterMessage;
use NotificationChannels\Twitter\TwitterStatusUpdate;

class Issued extends Notification implements ShouldQueue
{
    use Queueable;

    protected Token $token;

    public function __construct(Token $token)
    {
        $this->token = $token;
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
            ->subject(get_env_prefix().'New token issued')
            ->markdown('mail.notifications.nom.token.issued', [
                'user' => $notifiable,
                'token' => $this->token,
            ]);
    }

    public function toTwitter($notifiable): TwitterMessage
    {
        $accountName = $this->formatAddressName($this->token->owner);
        $link = route('explorer.token', [
            'zts' => $this->token->token_standard,
            'utm_source' => 'network_bot',
            'utm_medium' => 'twitter',
        ]);

        return new TwitterStatusUpdate("A new token has been created! {$this->token->name} was issued by {$accountName}

Token: $link

#ZenonNetworkAlert #Zenon");
    }

    private function formatAddressName(Account $account): string
    {
        if ($account->has_custom_label) {
            return $account->custom_label;
        }

        return short_hash($account->address);
    }
}
