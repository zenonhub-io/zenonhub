<?php

declare(strict_types=1);

namespace App\Notifications\Bots;

use App\Channels\DiscordWebhookChannel;
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

class BridgeAlert extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected AccountBlock $block)
    {
        $this->onQueue('alerts');
    }

    public function via($notifiable): array
    {
        $channels = [];

        if (config('bots.bridge-alerts.discord.enabled')) {
            $channels[] = DiscordWebhookChannel::class;
        }

        if (config('bots.bridge-alerts.telegram.enabled')) {
            $channels[] = TelegramChannel::class;
        }

        if (config('bots.bridge-alerts.twitter.enabled')) {
            $channels[] = TwitterChannel::class;
        }

        return $channels;
    }

    public function toDiscordWebhook($notifiable): DiscordWebhookMessage
    {
        $issuerAccount = $this->formatMarkdownAddressName($this->block->account);
        $action = $this->block->contractMethod->name;
        $contract = $this->block->toAccount->custom_label;

        $txLink = $this->formatMarkdownTxLink('discord');
        $adminLink = $this->formatMarkdownAddressLink($this->block->account, 'discord');
        $contractLink = $this->formatMarkdownAddressLink($this->block->toAccount, 'discord');

        return DiscordWebhookMessage::make()
            ->from('Zenon Bridge Alerts')
            ->embed(
                Embed::make()
                    ->color($this->getDiscordHighlightColour())
                    ->title(':robot: :rotating_light:')
                    ->description("**{$action}** was issued by {$issuerAccount}")
                    ->field('Data', "```
{$this->block->data->json}
```")
                    ->field('Transaction', $txLink)
                    ->field('Admin', $adminLink)
                    ->field($contract, $contractLink)
                    ->timestamp($this->block->created_at->format('c'))
            );
    }

    public function toTelegram($notifiable): TelegramMessage
    {
        $adminAccount = $this->formatMarkdownAddressName($this->block->account, '*');
        $action = $this->block->contractMethod->name;
        $contract = $this->block->toAccount->custom_label;

        $txLink = $this->formatMarkdownTxLink('telegram');
        $adminLink = $this->formatMarkdownAddressLink($this->block->account, 'telegram');
        $contractLink = $this->formatMarkdownAddressLink($this->block->toAccount, 'telegram');

        return TelegramMessage::create()
            ->token(config('bots.bridge-alerts.telegram.bot_token'))
            ->line("*{$action}* was issued by {$adminAccount}\n")
            ->line('*Data*')
            ->line("```
{$this->block->data->json}
```\n")
            ->line('*Transaction*')
            ->line("{$txLink}\n")
            ->line('*Admin*')
            ->line("{$adminLink}\n")
            ->line("*{$contract}*")
            ->line("{$contractLink}\n");
    }

    public function toTwitter($notifiable): TwitterMessage
    {
        $adminAccount = $this->formatMarkdownAddressName($this->block->account);
        $action = $this->block->contractMethod->name;
        $txLink = $this->formatMarkdownTxLink('twitter');

        return new TwitterStatusUpdate("{$action} was issued by {$adminAccount}

Tx: $txLink

#ZenonBridgeAlert #Zenon #NoM \$ZNN \$QSR");
    }

    //
    // Helpers

    private function formatTxLink(string $channel): string
    {
        return route('explorer.transaction.detail', [
            'hash' => $this->block->hash,
            'utm_source' => 'bridge_bot',
            'utm_medium' => $channel,
        ]);
    }

    private function formatAddressName(Account $account): string
    {
        if ($account->has_custom_label) {
            return $account->custom_label;
        }

        return 'an unknown address';
    }

    private function formatAddressLink(Account $account, string $channel): string
    {
        return route('explorer.account.detail', [
            'address' => $account->address,
            'utm_source' => 'bridge_bot',
            'utm_medium' => $channel,
        ]);
    }

    private function formatMarkdownTxLink(string $channel): string
    {
        $link = $this->formatTxLink($channel);

        return "[{$this->block->hash}]({$link})";
    }

    private function formatMarkdownAddressName(Account $account, $symbol = '**'): string
    {
        if ($account->has_custom_label) {
            return "{$symbol}{$account->custom_label}{$symbol}";
        }

        return "an {$symbol}unknown address{$symbol}";
    }

    private function formatMarkdownAddressLink(Account $account, string $channel): string
    {
        $link = $this->formatAddressLink($account, $channel);

        return "[{$account->address}]({$link})";
    }

    private function getDiscordHighlightColour(): int
    {
        if (in_array($this->block->contractMethod->name, ['Emergency'])) {
            return 0x8D2C2C; // Red
        }

        if (in_array($this->block->contractMethod->name, ['Halt'])) {
            return 0xCE7C4E; // Orange
        }

        if (in_array($this->block->contractMethod->name, ['Unhalt'])) {
            return 0x22C55E; // Green
        }

        // Grey
        return 0x607D8B;
    }
}
