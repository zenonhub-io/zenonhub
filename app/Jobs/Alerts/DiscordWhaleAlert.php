<?php

namespace App\Jobs\Alerts;

use App;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Token;
use App\Services\Discord\Embed;
use App\Services\Discord\Message;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class DiscordWhaleAlert implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public AccountBlock $block;

    protected bool $enabled;

    protected string $discordWebhook;

    public function __construct(AccountBlock $block)
    {
        $this->block = $block;
        $this->enabled = config('whale-bot.discord.enabled');
        $this->discordWebhook = config('whale-bot.discord.webhook');
    }

    public function handle(): void
    {
        if (! $this->discordWebhook || ! $this->enabled) {
            return;
        }

        Log::debug('Whale Bot Building Discord');

        $senderAccount = $this->formatAddressName($this->block->account);
        $receiverAccount = $this->formatAddressName($this->block->to_account);
        $amount = $this->block->token->getDisplayAmount($this->block->amount);
        $token = $this->block->token->symbol;

        $mainMessage = Message::make()
            ->from('Zenon Whale Bot')
            ->embed(Embed::make()
                ->color($this->getColour($this->block))
                ->title(':whale: :rotating_light:')
                ->description("**{$amount} {$token}** was sent from {$senderAccount} to {$receiverAccount}")
                ->field('Sender', $this->formatAddressLink($this->block->account))
                ->field('Receiver', $this->formatAddressLink($this->block->to_account))
                ->field('Transaction', $this->formatTransactionLink($this->block))
                ->timestamp($this->block->created_at->format('c'))
            );

        if ($this->block->to_account->name === 'BSC Bridge') {
            $mainMessage->embed(
                Embed::make()
                    ->color(0xF44336)
                    ->title(':rotating_light: --- Legacy Bridge Deprecation --- :rotating_light:')
                    ->description('The legacy bridge is being depreciated please see the [#legacy-bridge-deprecation](https://discord.com/channels/920058192560533504/1065658520462176256) channel for more details')
            );
        }

        try {
            App::make('discord.api', ['webhook' => $this->discordWebhook])->send($mainMessage);
            Log::debug('Whale Bot Sent Discord');
        } catch (\Exception $exception) {
            if ($exception->getCode() === 429) {
                Log::warning('Whale Bot - Discord rate limited');
                $this->release(2);
            } else {
                Log::warning('Whale Bot Error - '.$exception->getMessage());
            }
        }
    }

    private function formatAddressName(Account $account): string
    {
        if ($account->has_custom_label) {
            return "**{$account->custom_label}**";
        }

        if ($account->is_stex_trader) {
            $ending = mb_substr($account->address, -6);

            return "**STEX Trader (...{$ending})**";
        }

        return 'an **unknown address**';
    }

    private function formatAddressLink(Account $account): string
    {
        $link = route('explorer.account', [
            'address' => $account->address,
            'utm_source' => 'whale_bot',
            'utm_medium' => 'discord',
        ]);

        return "[{$account->address}]({$link})";
    }

    private function formatTransactionLink(AccountBlock $block): string
    {
        $link = route('explorer.transaction', [
            'hash' => $block->hash,
            'utm_source' => 'whale_bot',
            'utm_medium' => 'discord',
        ]);

        return "[{$block->hash}]({$link})";
    }

    private function getColour(AccountBlock $block): int
    {
        $colour = 0x607D8B; // Grey
        if ($block->token->token_standard === Token::ZTS_ZNN) {
            $colour = 0x6FF34D; // Zenon green
        } elseif ($block->token->token_standard === Token::ZTS_QSR) {
            $colour = 0x0061EB; // Zenon blue
        }

        if ($block->to_account->name === 'STEX Exchange') {
            $colour = 0xF44336;
        }

        return $colour;
    }
}
