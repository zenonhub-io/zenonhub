<?php

namespace App\Console\Commands;

use App;
use App\Models\Nom\Momentum;
use Illuminate\Console\Command;

class CheckHeight extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zenon:check-height';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks the current sync height against the database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $znn = App::make('zenon.api');

        $dbHeight = Momentum::count();
        $momentum = $znn->ledger->getFrontierMomentum()['data'];
        $networkHeight = $momentum->height;
        $syncingCount = $momentum->height - $dbHeight;

        $this->info('Sync height...');
        $this->line("Latest momentum height: {$networkHeight}");
        $this->line("Current database height: {$dbHeight}");
        $this->line("Still to sync: {$syncingCount}");
        $this->newLine();

        return self::SUCCESS;
    }
}
