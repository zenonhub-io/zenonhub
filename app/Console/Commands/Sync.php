<?php

namespace App\Console\Commands;

use App\Jobs\ProcessAccountBalance;
use App\Jobs\Sync\Pillars as SyncPillars;
use App\Jobs\Sync\Projects as SyncProjects;
use App\Jobs\Sync\ProjectStatus as SyncProjectStatus;
use App\Jobs\Sync\Sentinels as SyncSentinels;
use App\Jobs\Sync\Tokens as SyncTokens;
use App\Models\Nom\Account;
use DB;
use Illuminate\Console\Command;

class Sync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zenon:sync {type?*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncs the given item type with the latest network data';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $types = $this->argument('type');

        if (empty($types) || in_array('tokens', $types)) {
            $this->output->write('Saving tokens...');
            $this->output->newLine();

            SyncTokens::dispatch();
        }

        if (empty($types) || in_array('pillars', $types)) {
            $this->output->write('Saving pillars...');
            $this->output->newLine();

            SyncPillars::dispatch();
        }

        if (empty($types) || in_array('sentinels', $types)) {
            $this->output->write('Saving sentinels...');
            $this->output->newLine();

            SyncSentinels::dispatch();
        }

        if (empty($types) || in_array('az', $types)) {
            $this->output->write('Saving projects...');
            $this->output->newLine();

            SyncProjects::dispatch();
        }

        if (empty($types) || in_array('az-status', $types)) {
            $this->output->write('Saving project statuses...');
            $this->output->newLine();

            SyncProjectStatus::dispatch();
        }

        if (in_array('balances', $types)) {
            $accountCount = DB::table('nom_accounts')->count();
            $this->output->write('Saving Balances...');
            $this->output->newLine();
            $this->output->progressStart($accountCount);

            DB::table('nom_accounts')->orderBy('id')->chunk(100, function ($accounts) {
                foreach ($accounts as $account) {
                    $account = Account::findByAddress($account->address);
                    ProcessAccountBalance::dispatch($account);
                    $this->output->progressAdvance();
                }
            });

            $this->output->newLine();
        }

        return self::SUCCESS;
    }
}
