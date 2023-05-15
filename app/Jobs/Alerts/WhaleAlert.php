<?php

namespace App\Jobs\Alerts;

use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Token;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WhaleAlert implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public AccountBlock $block;

    protected int $znnValue;

    protected int $qsrValue;

    public function __construct(AccountBlock $block)
    {
        $this->block = $block;
        $this->znnValue = $this->getFormattedAmount(config('whale-bot.znn_cutoff'));
        $this->qsrValue = $this->getFormattedAmount(config('whale-bot.qsr_cutoff'));
    }

    public function handle(): void
    {
        if ($this->shouldSend()) {
            DiscordWhaleAlert::dispatch($this->block);
            TwitterWhaleAlert::dispatch($this->block);
        }
    }

    private function shouldSend(): bool
    {
        // No token dont send...
        if (! $this->block->token) {
            return false;
        }

        // Dont send alerts for stake contract under 5k ZNN
        if (
            ($this->block->account->address === Account::ADDRESS_STAKE || $this->block->to_account->address === Account::ADDRESS_STAKE)
            && $this->block->token->token_standard === Token::ZTS_ZNN
            && $this->block->amount < $this->getFormattedAmount(5000)
        ) {
            return false;
        }

        // If ZNN amount is over limit send
        if ($this->block->token->token_standard === Token::ZTS_ZNN && $this->block->amount >= $this->znnValue) {
            return true;
        }

        // If QSR amount is over limit send
        if ($this->block->token->token_standard === Token::ZTS_QSR && $this->block->amount >= $this->qsrValue) {
            return true;
        }

        return false;
    }

    private function getFormattedAmount(int $number): float
    {
        return $number * 100000000;
    }
}
