<?php

declare(strict_types=1);

use App\Enums\Nom\NetworkTokensEnum;
use App\Models\Nom\Momentum;
use App\Models\Nom\Token;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

Artisan::command('nom:reset-db', function () {
    Artisan::call('db:seed --class=DatabaseSeeder');
    Artisan::call('db:seed --class=NomSeeder');
    Artisan::call('db:seed --class=GenesisSeeder');
    Artisan::call('nom:create-or-update-latest-momentums-view');
    Artisan::call('nom:create-or-update-latest-account-blocks-view');
    Artisan::call('nom:create-or-update-embedded-contract-account-blocks-view');
})->purpose('Resets all NoM data back to genesis');

Artisan::command('site:after-deploy', function () {
    Artisan::call('nom:create-or-update-latest-momentums-view');
    Artisan::call('nom:create-or-update-latest-account-blocks-view');
    Artisan::call('nom:create-or-update-embedded-contract-account-blocks-view');
    Artisan::call('nom:update-contract-methods');
    Artisan::call('nom:update-named-addresses');
    Artisan::call('sync:orchestrators');
    Artisan::call('sync:bridge-status');
    Artisan::call('sync:public-nodes');
    Artisan::call('sync:pillar-metrics');
    Artisan::call('sync:token-prices');
    Artisan::call('site:generate-sitemap');
    Artisan::call('filament:optimize');
})->purpose('Sets up the site after a deploy');

Artisan::command('site:create-stats', function () {
    Artisan::call('sync:token-stats');
    Artisan::call('sync:network-stats');
    Artisan::call('sync:bridge-stats');
    Artisan::call('sync:project-status');
    Artisan::call('sync:pillar-engagement-scores');
})->purpose('Create initial stats for the site');

Artisan::command('indexer:remove-locks', function () {
    $lock = Cache::lock('indexerLock', 0, 'indexer');
    $emergencyLock = Cache::lock('indexerEmergencyLock', 0, 'indexer');
    $lock->release();
    $emergencyLock->release();
});

Schedule::call(function () {

    $date = now()->subDay()->endOfDay();

    App\Actions\Sync\BridgeStats::run($date);
    App\Actions\Sync\NetworkStats::run($date);

    $tokens = Token::whereIn('token_standard', [
        NetworkTokensEnum::ZNN->zts(),
        NetworkTokensEnum::QSR->zts(),
    ])->get();

    $tokens->each(function (Token $token) use ($date): void {
        App\Actions\Sync\TokenStats::run($token, $date);
    });

})->dailyAt('01:00');

Schedule::command('sync:public-nodes')->cron('5 */6 * * *');

if (app()->environment('production')) {
    Schedule::command('indexer:run')
        ->everyTenSeconds()
        ->withoutOverlapping(3)
        ->runInBackground();

    Schedule::call(function () {
        Artisan::call('sync:pillar-metrics');
        Artisan::call('sync:pillar-stats');
        Artisan::call('sync:token-prices');
        Artisan::call('sync:bridge-status');
        Artisan::call('sync:orchestrators');

        Artisan::call('plasma-bot:cancel-expired');
        Artisan::call('plasma-bot:receive-all');

        // Check the indexer has inserted a momentum in the last 15 minutes
        $latestMomentum = Momentum::getFrontier();
        if ($latestMomentum->created_at < now()->subMinutes(15)) {
            Log::critical('Indexer has stopped running, last momentum:', [
                'height' => $latestMomentum->height,
                'date' => $latestMomentum->created_at->format('Y-m-d H:i:s'),
            ]);
        }
    })->everyFiveMinutes();

    Schedule::command('site:generate-sitemap')->daily();

} else {
    Schedule::command('indexer:run')
        ->everyMinute()
        ->withoutOverlapping(3)
        ->runInBackground();

    Schedule::call(function () {
        Artisan::call('sync:pillar-metrics');
        Artisan::call('sync:pillar-stats');
        Artisan::call('sync:token-prices');
        Artisan::call('sync:bridge-status');
        Artisan::call('sync:orchestrators');
    })->hourly();
}
