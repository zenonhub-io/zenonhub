<?php

declare(strict_types=1);

namespace App\Console\Commands\Db;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateOrUpdateLatestAccountBlocksView extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:create-or-update-latest-account-blocks-view';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create or update latest account blocks view';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DB::statement('DROP VIEW IF EXISTS view_latest_nom_account_blocks');
        DB::statement('
            CREATE VIEW view_latest_nom_account_blocks AS
                SELECT *
                FROM nom_account_blocks
                WHERE to_account_id != 1 AND (contract_method_id NOT IN (36, 68) OR contract_method_id IS null)
                ORDER BY id DESC
                LIMIT 50000
        ');
    }
}
