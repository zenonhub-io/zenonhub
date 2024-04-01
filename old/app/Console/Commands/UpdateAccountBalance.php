<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domains\Nom\Models\Account;
use App\Jobs\ProcessAccountBalance;
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
        $account = Account::findBy('address', $address);

        if ($account) {
            $this->info('Update account balance job queued');
            ProcessAccountBalance::dispatch($account);

            return self::SUCCESS;
        }

        $this->error('Invalid address');

        return self::FAILURE;
    }
}
