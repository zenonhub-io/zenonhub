<?php

namespace App\Jobs\Alerts;

use App;
use Log;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Token;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\Discord\Embed;
use App\Services\Discord\Message;

class WhaleAlert implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $backoff = 5;
    public AccountBlock $block;

    protected int $znnValue;
    protected int $qsrValue;
    protected string $discordWebhook;
    //protected ?string $twitterWebhook;

    public function __construct(AccountBlock $block)
    {
        $this->block = $block;
        $this->znnValue = $this->getCutoff(config('whale-bot.znn_cutoff'));
        $this->qsrValue = $this->getCutoff(config('whale-bot.qsr_cutoff'));
        $this->discordWebhook = config('whale-bot.discord_webhook');
    }

    public function handle(): void
    {
        if ($this->shouldSend()) {
            $this->sendToDiscord();
            $this->sendToTwitter();
        }
    }

    private function shouldSend(): bool
    {
        if (! $this->block->token) {
            return false;
        }

        if ($this->block->token->token_standard === Token::ZTS_ZNN && $this->block->amount > $this->znnValue) {
            return true;
        }

        if ($this->block->token->token_standard === Token::ZTS_QSR && $this->block->amount > $this->qsrValue) {
            return true;
        }

        return false;
    }

    private function sendToDiscord(): void
    {
        $senderAccount = $this->formatAddressForDiscord($this->block->account);
        $receiverAccount = $this->formatAddressForDiscord($this->block->to_account);;
        $amount = $this->block->token->getDisplayAmount($this->block->amount);
        $token = $this->block->token->symbol;

        $colour = 0x607d8b; // Grey
        if ($this->block->token->token_standard === Token::ZTS_ZNN) {
            $colour = 0x6FF34D; // Zenon green
        } elseif ($this->block->token->token_standard === Token::ZTS_QSR) {
            $colour = 0x0061EB; // Zenon blue
        }

        $senderLink = route('explorer.account', ['address' => $this->block->account->address]);
        $receiverLink = route('explorer.account', ['address' => $this->block->to_account->address]);

        $mainMessage = Message::make()
            ->from("Zenon Whale Bot")
            ->embed(Embed::make()
                ->color($colour)
                ->title(':whale: :rotating_light:')
                ->description("**{$amount} {$token}** was sent from {$senderAccount} to {$receiverAccount}")
                ->field('Sender', "[{$this->block->account->address}]({$senderLink})")
                ->field('Receiver', "[{$this->block->to_account->address}]({$receiverLink})")
                ->field('Link', route('explorer.transaction', ['hash' => $this->block->hash]))
                ->timestamp($this->block->created_at->format('c'))
            );
        try {
            App::make('discord.api', ['webhook' => $this->discordWebhook])->send($mainMessage);
        } catch (\Exception $exception) {
            if ($exception->getCode() === 429) {
                Log::warning('Whale Bot - Discord rate limited');
                $this->release(2);
            }
        }
    }

    private function sendToTwitter(): void
    {

    }

    private function getCutoff(int $number): float
    {
        return ($number * 100000000);
    }

    private function formatAddressForDiscord($account): string
    {
        $namedAddresses = array_flip(config('zenon.named_accounts'));
        if ($account->address === $namedAddresses['STEX Exchange']) {
            $formattedLink = "**[{$account->named_address}](https://app.stex.com?ref=zenonhub)**";
        } else {
            $formattedLink = ($account->is_named_address
                ? "**{$account->named_address}**"
                : "an **unknown address**");
        }

        return $formattedLink;
    }
}
