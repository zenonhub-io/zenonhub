<?php

namespace App\Jobs;

use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\AccountReward;
use App\Models\Nom\Token;
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
        $this->onQueue('indexer');
    }

    public function handle(): void
    {
        $liquidityProgramDistributor = Account::where('name', 'Liquidity Program Distributor')->first();

        if ($this->block->account->id === $liquidityProgramDistributor->id && $this->block->token->token_standard === Token::ZTS_QSR) {
            AccountReward::create([
                'account_id' => $this->block->to_account->id,
                'token_id' => $this->block->token->id,
                'type' => AccountReward::TYPE_LIQUIDITY_PROGRAM,
                'amount' => $this->block->amount,
                'created_at' => $this->block->created_at,
            ]);
        }
    }
}
