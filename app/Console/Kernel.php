<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $this->runIndexer($schedule);

        $schedule->command('horizon:snapshot')->everyFiveMinutes();
        $schedule->command('zenon:sync pillars')->everyFiveMinutes();
        $schedule->command('zenon:check-indexer')->everyFifteenMinutes();
        $schedule->command('plasma-bot:clear-expired')->everyFifteenMinutes();
        $schedule->command('plasma-bot:receive-all')->everyFifteenMinutes();
        $schedule->command('zenon:sync az-status')->hourly();
        $schedule->command('zenon:update-znn-price')->hourly();
        $schedule->command('queue:prune-batches')->daily();
        $schedule->command('site:generate-sitemap')->daily();

        $schedule->command('zenon:update-node-list')->cron('5 */6 * * *');
    }

    private function runIndexer(Schedule $schedule): void
    {
        if (! config('explorer.enable_indexer')) {
            return;
        }

        $command = 'zenon:index';

        if (app()->environment('production')) {
            $command .= ' --alerts=true --balances=true';
        }

        if (app()->environment('staging')) {
            $command .= ' --alerts=false --balances=true';
        }

        $schedule->command($command)->everyTenSeconds()->runInBackground();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
