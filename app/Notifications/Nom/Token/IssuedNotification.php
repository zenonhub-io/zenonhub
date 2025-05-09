<?php

declare(strict_types=1);

namespace App\Notifications\Nom\Token;

use App\Bots\NetworkAlertBot;
use App\Models\Nom\Token;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twitter\TwitterChannel;
use NotificationChannels\Twitter\TwitterMessage;
use NotificationChannels\Twitter\TwitterStatusUpdate;

class IssuedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Token $token;

    public function __construct(Token $token)
    {
        $this->onQueue('notifications');
        $this->token = $token;
    }

    public function via($notifiable): array
    {
        $channels = [];

        if ($notifiable instanceof NetworkAlertBot) {
            if (config('bots.network-alerts.twitter.enabled')) {
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
            ->subject(get_env_prefix() . 'New token issued')
            ->markdown('mail.notifications.nom.token.issued', [
                'user' => $notifiable,
                'token' => $this->token,
                'link' => $this->getItemLink(),
            ]);
    }

    public function toTwitter($notifiable): TwitterMessage
    {
        $accountName = short_address($this->token->owner);
        $link = $this->getItemLink('twitter');

        return new TwitterStatusUpdate("ℹ️ A new token has been issued! {$this->token->name} was created by {$accountName}

🔗 $link");
    }

    private function getItemLink(string $source = 'email'): string
    {
        return route('explorer.token.detail', [
            'zts' => $this->token->token_standard,
            'utm_source' => 'network_bot',
            'utm_medium' => $source,
        ]);
    }
}
