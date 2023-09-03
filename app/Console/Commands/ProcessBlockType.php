<?php

namespace App\Console\Commands;

use App\Models\Nom\AccountBlock;
use App\Models\Nom\ContractMethod;
use Illuminate\Console\Command;

class ProcessBlockType extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zenon:process-block-type {type*} {--alerts=false} {--balances=false}';

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
        $blockTypes = $this->argument('type');
        $alerts = $this->option('alerts');
        $balances = $this->option('balances');
        $methods = ContractMethod::whereIn('id', $blockTypes)->get();

        if ($alerts) {
            $alerts = filter_var($alerts, FILTER_VALIDATE_BOOLEAN);
        }

        if ($balances) {
            $balances = filter_var($balances, FILTER_VALIDATE_BOOLEAN);
        }

        $this->info('Processing block types...');
        $methods->each(function ($method) {
            $this->line("{$method->contract->name}.{$method->name}");
        })->implode(', ');
        $this->newLine();

        if ($this->confirm('Do you wish to continue?')) {
            $bar = $this->output->createProgressBar(
                AccountBlock::whereIn('contract_method_id', $blockTypes)->count()
            );
            $bar->start();

            AccountBlock::whereIn('contract_method_id', $blockTypes)
                ->orderBy('id', 'ASC')
                ->chunk(100, function ($chunk) use ($bar, $alerts, $balances) {
                    foreach ($chunk as $block) {
                        (new \App\Actions\ProcessBlock(
                            $block,
                            $alerts,
                            $balances
                        ))->execute();
                        $bar->advance();
                    }
                });

            $bar->finish();
        }

        $this->newLine();

        return self::SUCCESS;
    }
}
