<?php

declare(strict_types=1);

use App\Domains\Nom\Models\Momentum;
use Illuminate\Support\Facades\Artisan;

Artisan::command('nom:reset-db', function () {
    Artisan::call('migrate:rollback');
    Artisan::call('migrate');
    Artisan::call('db:seed --class=DatabaseSeeder');
    Artisan::call('db:seed --class=NomBaseSeeder');
    Artisan::call('db:seed --class=GenesisSeeder');
})->purpose('Resets all NoM data back to genesis');

Schedule::call(function () {
    // Check the indexer has inserted a momentum in the last 15 minutes
    $latestMomentum = Momentum::getFrontier();
    if ($latestMomentum->created_at < now()->subMinutes(15)) {
        Log::critical('Indexer has stopped running, last momentum:', [
            'height' => $latestMomentum->height,
            'date' => $latestMomentum->created_at->format('Y-m-d H:i:s'),
        ]);
    }
})->everyFiveMinutes();

Schedule::command('nom:sync-pillar-metrics')->everyFiveMinutes();
Schedule::command('nom:sync-orchestrators')->everyFiveMinutes();
Schedule::command('nom:sync-nodes')->cron('5 */6 * * *');

Schedule::command('site:generate-sitemap')->daily();
