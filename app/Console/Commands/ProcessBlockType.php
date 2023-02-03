<?php

namespace App\Console\Commands;

use App\Jobs\ProcessBlock;
use App\Models\Nom\AccountBlockData;
use App\Models\Nom\ContractMethod;
use Illuminate\Console\Command;

class ProcessBlockType extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zenon:process-block-type {type*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reprocesses all blocks of the given contract method ID';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $blockTypes = $this->argument('type');
        $methods = ContractMethod::whereIn('id', $blockTypes)->get();

        $this->info("Processing block types...");
        $methods->each(function ($method) {
            $this->line("{$method->contract->name}.{$method->name}");
        })->implode(', ');
        $this->newLine();

        if ($this->confirm('Do you wish to continue?')) {
            $bar = $this->output->createProgressBar(
                AccountBlockData::whereIn('contract_method_id', $blockTypes)->count()
            );
            $bar->start();

            AccountBlockData::whereIn('contract_method_id', $blockTypes)
                ->orderBy('id', 'ASC')
                ->chunk(100, function ($chunk) use ($bar) {
                    foreach ($chunk as $blockData) {
                        ProcessBlock::dispatch($blockData->account_block);
                        $bar->advance();
                    }
                });

            $bar->finish();
        }

        $this->newLine();

        return self::SUCCESS;
    }
}
