<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Nom\Account;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateOrUpdateEmbeddedContractAccountBlocksViewCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nom:create-or-update-embedded-contract-account-blocks-view';

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
        $contracts = Account::whereEmbedded()->get();
        $contracts->each(function (Account $account) {

            $contractName = Str::snake($account->name);
            $viewName = "view_nom_account_blocks_{$contractName}";

            DB::statement('DROP VIEW IF EXISTS ' . $viewName);
            DB::statement("
                CREATE VIEW {$viewName} AS
                    SELECT *
                    FROM nom_account_blocks
                    WHERE account_id = {$account->id} OR to_account_id = {$account->id}
                    ORDER BY id DESC
            ");
        });
    }
}
