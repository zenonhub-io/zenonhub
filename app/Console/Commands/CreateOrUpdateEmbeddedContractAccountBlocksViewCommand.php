<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Nom\Account;
use App\Models\Nom\ContractMethod;
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
        $contracts = Account::whereEmbedded()->orWhere('address', config('explorer.burn_address'))->get();

        $updateContractMethodIds = ContractMethod::where('name', 'Update')
            ->pluck('id')
            ->implode(',');

        $contracts->each(function (Account $account) use ($updateContractMethodIds) {

            $contractName = Str::slug($account->name, '_');
            $viewName = "view_nom_account_blocks_{$contractName}";

            DB::statement('DROP VIEW IF EXISTS ' . $viewName);
            DB::statement("
                CREATE VIEW {$viewName} AS
                    SELECT *
                    FROM nom_account_blocks
                    WHERE account_id = {$account->id} OR to_account_id = {$account->id}
                    AND (contract_method_id IS NULL OR contract_method_id NOT IN ({$updateContractMethodIds}) )
                    ORDER BY id DESC
            ");
        });
    }
}
