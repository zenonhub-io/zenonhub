<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateContractMethods extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zenon:update-contract-methods';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds any new missing contracts & methods';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        (new \App\Actions\UpdateContractMethods())->execute();

        return self::FAILURE;
    }
}
