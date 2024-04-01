<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domains\Nom\Enums\AccountRewardTypesEnum;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\AccountReward;
use Illuminate\Console\Command;

class ProcessLiquidityProgramRewards extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zenon:process-liquidity-program-rewards';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reprocesses all blocks of the given contract method ID';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Processing liquidity rewards...');
        $this->newLine();

        $query = AccountBlock::where('token_id', 2)->whereHas('account',
            fn ($q) => $q->where('address', config('explorer.liquidity_program_distributor'))
        );

        if ($this->confirm('Do you wish to continue?')) {
            $bar = $this->output->createProgressBar(
                $query->count()
            );
            $bar->start();

            $query->orderBy('id', 'ASC')
                ->chunk(100, function ($chunk) use ($bar) {
                    foreach ($chunk as $block) {

                        AccountReward::create([
                            'chain_id' => $block->chain_id,
                            'account_id' => $block->to_account_id,
                            'token_id' => $block->token_id,
                            'type' => AccountRewardTypesEnum::LIQUIDITY_PROGRAM->value,
                            'amount' => $block->amount,
                            'created_at' => $block->created_at,
                        ]);

                        $bar->advance();
                    }
                });

            $bar->finish();
        }

        $this->newLine();

        return self::SUCCESS;
    }
}
