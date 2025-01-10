<?php

declare(strict_types=1);

namespace App\Notifications\Bots;

use App\Channels\DiscordWebhookChannel;
use App\Enums\Nom\NetworkTokensEnum;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Services\Discord\Embed;
use App\Services\Discord\Message as DiscordWebhookMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;
use NotificationChannels\Twitter\TwitterChannel;
use NotificationChannels\Twitter\TwitterMessage;
use NotificationChannels\Twitter\TwitterStatusUpdate;

class WhaleAlert extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected AccountBlock $block)
    {
        $this->onQueue('alerts');
    }

    public function via($notifiable): array
    {
        $channels = [];

        if (config('bots.whale-alerts.discord.enabled')) {
            $channels[] = DiscordWebhookChannel::class;
        }

        if (config('bots.whale-alerts.telegram.enabled')) {
            $channels[] = TelegramChannel::class;
        }

        if (config('bots.whale-alerts.twitter.enabled')) {
            $channels[] = TwitterChannel::class;
        }

        return $channels;
    }

    public function toDiscordWebhook($notifiable): DiscordWebhookMessage
    {
        $senderAccount = $this->formatMarkdownAddressName($this->block->account);
        $receiverAccount = $this->formatMarkdownAddressName($this->block->toAccount);
        $amount = $this->block->token->getFormattedAmount($this->block->amount);
        $token = $this->block->token->symbol;

        return DiscordWebhookMessage::make()
            ->from('Zenon Whale Bot')
            ->embed(
                Embed::make()
                    ->color($this->getDiscordHighlightColour())
                    ->title(':whale: :rotating_light:')
                    ->description("**{$amount} \${$token}** was sent from {$senderAccount} to {$receiverAccount}")
                    ->field('Sender', $this->formatMarkdownAddressLink($this->block->account, 'discord'))
                    ->field('Receiver', $this->formatMarkdownAddressLink($this->block->toAccount, 'discord'))
                    ->field('Transaction', $this->formatMarkdownTxLink('discord'))
                    ->timestamp($this->block->created_at->format('c'))
            );
    }

    public function toTelegram($notifiable): TelegramMessage
    {
        $senderAccount = $this->formatMarkdownAddressName($this->block->account, '*');
        $receiverAccount = $this->formatMarkdownAddressName($this->block->toAccount, '*');
        $amount = $this->block->token->getFormattedAmount($this->block->amount);
        $token = $this->block->token->symbol;

        $senderLink = $this->formatMarkdownAddressLink($this->block->account, 'telegram');
        $receiverLink = $this->formatMarkdownAddressLink($this->block->toAccount, 'telegram');
        $txLink = $this->formatMarkdownTxLink('telegram');

        return TelegramMessage::create()
            ->token(config('bots.whale-alerts.telegram.bot_token'))
            ->line("*{$amount} \${$token}* was sent from {$senderAccount} to {$receiverAccount}\n")
            ->line('*Transaction*')
            ->line("{$txLink}\n")
            ->line('*Sender*')
            ->line("{$senderLink}\n")
            ->line('*Receiver*')
            ->line("{$receiverLink}\n");
    }

    public function toTwitter($notifiable): TwitterMessage
    {
        $senderAccount = $this->formatAddressName($this->block->account);
        $receiverAccount = $this->formatAddressName($this->block->toAccount);
        $amount = $this->block->token->getFormattedAmount($this->block->amount);
        $token = $this->block->token->symbol;
        $txLink = $this->formatTxLink('twitter');

        return new TwitterStatusUpdate("{$amount} \${$token} was sent from {$senderAccount} to {$receiverAccount}

Tx: $txLink");
    }

    //
    // Helpers

    private function formatTxLink(string $channel): string
    {
        return route('explorer.transaction.detail', [
            'hash' => $this->block->hash,
            'utm_source' => 'whale_bot',
            'utm_medium' => $channel,
        ]);
    }

    private function formatMarkdownTxLink(string $channel): string
    {
        $link = $this->formatTxLink($channel);

        return "[{$this->block->hash}]({$link})";
    }

    private function formatAddressName(Account $account): string
    {
        if ($account->has_custom_label) {
            return $account->custom_label;
        }

        return 'an unknown address';
    }

    private function formatMarkdownAddressName(Account $account, $symbol = '**'): string
    {
        if ($account->has_custom_label) {
            return "{$symbol}{$account->custom_label}{$symbol}";
        }

        return "an {$symbol}unknown address{$symbol}";
    }

    private function formatAddressLink(Account $account, string $channel): string
    {
        return route('explorer.account.detail', [
            'address' => $account->address,
            'utm_source' => 'whale_bot',
            'utm_medium' => $channel,
        ]);
    }

    private function formatMarkdownAddressLink(Account $account, string $channel): string
    {
        $link = $this->formatAddressLink($account, $channel);

        return "[{$account->address}]({$link})";
    }

    private function getDiscordHighlightColour(): int
    {
        $colour = 0x607D8B; // Grey
        if ($this->block->token->token_standard === NetworkTokensEnum::ZNN->value) {
            $colour = config('zenon-hub.colours.zenon-green');
        } elseif ($this->block->token->token_standard === NetworkTokensEnum::QSR->value) {
            $colour = config('zenon-hub.colours.zenon-blue');
        }

        return (int) $colour;
    }
}
