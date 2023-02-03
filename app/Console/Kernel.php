<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('zenon:sync pillars')->everyFiveMinutes();
        $schedule->command('zenon:process-new-projects')->everyFiveMinutes();
        $schedule->command('zenon:update-znn-price')->everyFifteenMinutes();
        $schedule->command('zenon:check-indexer')->everyFifteenMinutes();
        $schedule->command('zenon:project-voting-reminder')->hourly();
        $schedule->command('zenon:update-node-list')->cron('5 */6 * * *');
        $schedule->command('queue:prune-batches')->daily();
        $schedule->command('sitemap:generate')->daily();
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
