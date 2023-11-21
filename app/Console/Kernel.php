<?php

namespace App\Console;

use App\Actions\Nom\Accelerator\SendPhaseVotingReminders;
use App\Actions\Nom\Accelerator\SendProjectVotingReminders;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $this->runIndexer($schedule);

        $schedule->command('horizon:snapshot')->everyFiveMinutes();
        $schedule->command('zenon:sync pillars orchestrators')->everyFiveMinutes();
        $schedule->command('zenon:check-indexer')->everyFifteenMinutes();
        $schedule->command('plasma-bot:clear-expired')->everyFifteenMinutes();
        $schedule->command('plasma-bot:receive-all')->everyFifteenMinutes();
        $schedule->command('zenon:sync az-status')->hourly();
        $schedule->command('zenon:update-znn-price')->hourly();
        $schedule->command('queue:prune-batches')->daily();
        $schedule->command('site:generate-sitemap')->daily();

        $schedule->call(function () {
            (new SendProjectVotingReminders())->execute();
        })->dailyAt('16:05')->environments('production');

        $schedule->call(function () {
            (new SendPhaseVotingReminders())->execute();
        })->dailyAt('17:05')->environments('production');

        $schedule->command('zenon:update-node-list')->cron('5 */6 * * *');
    }

    private function runIndexer(Schedule $schedule): void
    {
        if (! config('explorer.enable_indexer')) {
            return;
        }

        $alerts = config('explorer.enable_alerts') ? 'true' : 'false';
        $balances = config('explorer.enable_balances') ? 'true' : 'false';
        $command = "zenon:index --alerts={$alerts} --balances={$balances}";

        $schedule->command($command)
            ->everyTenSeconds()
            ->withoutOverlapping()
            ->runInBackground();
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
