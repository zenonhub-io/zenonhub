<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Nom\Account;
use App\Models\Nom\ContractMethod;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateOrUpdateLatestAccountBlocksViewCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nom:create-or-update-latest-account-blocks-view';

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
        $burnAddress = config('explorer.burn_address');
        $burnAccount = Account::firstWhere('address', $burnAddress);

        $updateContractMethodIds = ContractMethod::where('name', 'Update')
            ->pluck('id')
            ->implode(',');

        DB::statement('DROP VIEW IF EXISTS view_latest_nom_account_blocks');
        DB::statement("
            CREATE VIEW view_latest_nom_account_blocks AS
                SELECT *
                FROM nom_account_blocks
                WHERE to_account_id != {$burnAccount->id}
                AND (contract_method_id IS NULL OR contract_method_id NOT IN ({$updateContractMethodIds}) )
                ORDER BY id DESC
                LIMIT 50000
        ");
    }
}
