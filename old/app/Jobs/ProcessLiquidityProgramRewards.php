<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Domains\Nom\Enums\AccountRewardTypesEnum;
use App\Domains\Nom\Enums\NetworkTokensEnum;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\AccountReward;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessLiquidityProgramRewards implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 25;

    public AccountBlock $block;

    public function __construct(AccountBlock $block)
    {
        $this->block = $block;
    }

    public function handle(): void
    {
        if ($this->block->account->address === config('explorer.liquidity_program_distributor') && $this->block->token->token_standard === NetworkTokensEnum::QSR->value) {
            AccountReward::create([
                'account_id' => $this->block->toAccount->id,
                'token_id' => $this->block->token->id,
                'type' => AccountRewardTypesEnum::LIQUIDITY_PROGRAM->value,
                'amount' => $this->block->amount,
                'created_at' => $this->block->created_at,
            ]);
        }
    }
}
