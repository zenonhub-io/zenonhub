<?php

namespace App\Console\Commands;

use App\Models\Nom\Account;
use Illuminate\Console\Command;

class UpdateNamedAddresses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zenon:update-named-addresses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates any named addresses from the config array';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Updating named addresses');
        $namedAccounts = config('explorer.named_accounts');

        foreach ($namedAccounts as $address => $name) {
            $address = Account::findByAddress($address);
            if ($address) {
                $address->name = $name;
                $address->save();
            }
        }

        return self::SUCCESS;
    }
}
