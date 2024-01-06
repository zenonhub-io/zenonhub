<?php

namespace App\Console;

use App\Actions\ClearBridgeStatusCache;
use App\Actions\GenerateSitemap;
use App\Actions\Nom\Accelerator\SendPhaseVotingReminders;
use App\Actions\Nom\Accelerator\SendProjectVotingReminders;
use App\Actions\Nom\Accelerator\UpdateProjectFunding;
use App\Actions\PlasmaBot\CancelExpired;
use App\Actions\PlasmaBot\ReceiveAll;
use App\Actions\UpdateTokenPrices;
use App\Exceptions\ApplicationException;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $this->runIndexer($schedule);

        //
        // All environments

        $schedule->command('zenon:sync pillars orchestrators')->everyFiveMinutes();
        $schedule->command('zenon:check-indexer')->everyFifteenMinutes();
        $schedule->command('zenon:sync az-status')->hourly();
        $schedule->command('queue:prune-batches')->daily();
        $schedule->command('zenon:sync nodes')->cron('5 */6 * * *');

        $schedule->call(fn () => (new ClearBridgeStatusCache())->execute())->everyFiveMinutes();
        $schedule->call(fn () => (new UpdateProjectFunding())->execute())->hourly();
        $schedule->call(fn () => (new GenerateSitemap())->execute())->daily();

        //
        // Production

        $schedule->command('horizon:snapshot')->everyFiveMinutes()->environments('production');
        $schedule->call(function () {
            try {
                (new UpdateTokenPrices())->execute();
            } catch (ApplicationException $exception) {
                Log::warning($exception);
            }
        })->everyFiveMinutes()->environments('production');

        $schedule->call(function () {
            (new CancelExpired())->execute();
            (new ReceiveAll())->execute();
        })->everyFifteenMinutes()->environments('production');

        $schedule->call(fn () => (new SendProjectVotingReminders())->execute())
            ->at('16:05')
            ->days(Schedule::MONDAY, Schedule::WEDNESDAY, Schedule::FRIDAY)
            ->environments('production');

        $schedule->call(fn () => (new SendPhaseVotingReminders())->execute())
            ->at('16:05')
            ->days(Schedule::TUESDAY, Schedule::THURSDAY)
            ->environments('production');

        //
        // Staging

        $schedule->call(fn () => (new UpdateTokenPrices())->execute())->dailyAt('00:07')->environments('staging');
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
            ->withoutOverlapping(3)
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
