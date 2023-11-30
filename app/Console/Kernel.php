<?php

namespace App\Console;

use App\Actions\Nom\Accelerator\SendPhaseVotingReminders;
use App\Actions\Nom\Accelerator\SendProjectVotingReminders;
use App\Actions\Nom\Accelerator\UpdateProjectFunding;
use App\Actions\PlasmaBot\CancelExpired;
use App\Actions\PlasmaBot\ReceiveAll;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $this->runIndexer($schedule);

        $schedule->command('horizon:snapshot')->everyFiveMinutes()->environments('production');
        $schedule->command('zenon:sync pillars orchestrators')->everyFiveMinutes();
        $schedule->command('zenon:check-indexer')->everyFifteenMinutes();
        $schedule->command('zenon:sync az-status')->hourly();
        $schedule->command('zenon:update-token-prices')->hourly();
        $schedule->command('queue:prune-batches')->daily();
        $schedule->command('site:generate-sitemap')->daily();

        $schedule->call(function () {
            (new CancelExpired())->execute();
            (new ReceiveAll())->execute();
        })->everyFifteenMinutes()->environments('production');

        $schedule->call(function () {
            (new UpdateProjectFunding())->execute();
        })->hourly();

        $schedule->call(function () {
            (new SendProjectVotingReminders())->execute();
        })
            ->at('16:05')
            ->days(Schedule::MONDAY, Schedule::WEDNESDAY, Schedule::FRIDAY)
            ->environments('production');

        $schedule->call(function () {
            (new SendPhaseVotingReminders())->execute();
        })
            ->at('16:05')
            ->days(Schedule::TUESDAY, Schedule::THURSDAY)
            ->environments('production');

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
