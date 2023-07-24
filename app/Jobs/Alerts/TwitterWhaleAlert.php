<?php

namespace App\Jobs\Alerts;

use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Services\Twitter;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class TwitterWhaleAlert implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public AccountBlock $block;

    protected bool $enabled;

    protected ?Twitter $twitter;

    public function __construct(AccountBlock $block)
    {
        $this->block = $block;
        $this->enabled = config('whale-alerts.twitter.enabled');
        $this->twitter = App::make('twitter.api', [
            'api_key' => config('whale-alerts.twitter.api_key'),
            'api_key_secret' => config('whale-alerts.twitter.api_key_secret'),
            'access_token' => config('whale-alerts.twitter.access_token'),
            'access_token_secret' => config('whale-alerts.twitter.access_token_secret'),
        ]);
    }

    public function handle(): void
    {
        if (! $this->enabled) {
            return;
        }

        Log::debug('Whale Bot building twitter');

        $senderAccount = $this->formatAddressName($this->block->account);
        $receiverAccount = $this->formatAddressName($this->block->to_account);
        $amount = $this->block->token->getDisplayAmount($this->block->amount);
        $token = $this->block->token->symbol;
        $link = route('explorer.transaction', [
            'hash' => $this->block->hash,
            'utm_source' => 'whale_bot',
            'utm_medium' => 'twitter',
        ]);

        $this->twitter->tweet("{$amount} \${$token} was sent from {$senderAccount} to {$receiverAccount}

Tx: $link

#ZenonWhaleAlert #Zenon #Bitcoin #NoM \$ZNN \$QSR \$BTC");
    }

    private function formatAddressName(Account $account): string
    {
        if ($account->has_custom_label) {
            return $account->custom_label;
        }

        if ($account->is_stex_trader) {
            $ending = mb_substr($account->address, -6);

            return "STEX Trader (...{$ending})";
        }

        return 'an unknown address';
    }
}
