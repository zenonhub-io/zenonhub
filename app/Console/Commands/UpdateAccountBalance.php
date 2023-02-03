<?php

namespace App\Console\Commands;

use App\Jobs\ProcessAccountBalance;
use App\Models\Nom\Account;
use Illuminate\Console\Command;

class UpdateAccountBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zenon:update-account-balance {address}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the given address balance';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $address = $this->argument('address');
        $account = Account::findByAddress($address);

        if($account) {
            $this->info("Update account balance job queued");
            ProcessAccountBalance::dispatch($account);
            return self::SUCCESS;
        }

        $this->error("Invalid address");

        return self::FAILURE;
    }
}
