<?php

declare(strict_types=1);

use App\Enums\Nom\NetworkTokensEnum;
use App\Models\Nom\Momentum;
use App\Models\Nom\Token;
use Illuminate\Support\Facades\Artisan;

Artisan::command('nom:reset-db', function () {
    Artisan::call('migrate:rollback');
    Artisan::call('migrate');
    Artisan::call('db:seed --class=DatabaseSeeder');
    Artisan::call('db:seed --class=NomBaseSeeder');
    Artisan::call('db:seed --class=GenesisSeeder');
})->purpose('Resets all NoM data back to genesis');

Schedule::command('indexer:run')
    ->everyTenSeconds()
    ->withoutOverlapping(3)
    ->runInBackground();

Schedule::call(function () {
    App\Actions\Sync\BridgeStatus::run();
    App\Actions\Sync\Orchestrators::run();

    Artisan::call('sync:pillar-metrics');
    Artisan::call('sync:pillar-stats');
    Artisan::call('sync:token-prices');

    App\Actions\PlasmaBot\CancelExpired::run();
    App\Actions\PlasmaBot\ReceiveAll::run();

    // Check the indexer has inserted a momentum in the last 15 minutes
    $latestMomentum = Momentum::getFrontier();
    if ($latestMomentum->created_at < now()->subMinutes(15)) {
        Log::critical('Indexer has stopped running, last momentum:', [
            'height' => $latestMomentum->height,
            'date' => $latestMomentum->created_at->format('Y-m-d H:i:s'),
        ]);
    }
})->everyFiveMinutes();

Schedule::call(function () {

    $date = now()->subDay()->endOfDay();

    App\Actions\Sync\BridgeStats::run($date);
    App\Actions\Sync\NetworkStats::run($date);

    $tokens = Token::whereIn('token_standard', [
        NetworkTokensEnum::ZNN->value,
        NetworkTokensEnum::QSR->value,
    ])->get();

    $tokens->each(function (Token $token) use ($date): void {
        App\Actions\Sync\TokenStats::run($token, $date);
    });

})->dailyAt('01:00');

Schedule::command('sync:public-nodes')->cron('5 */6 * * *');

Schedule::command('site:generate-sitemap')->daily();

Schedule::command('horizon:snapshot')
    ->everyFiveMinutes()
    ->environments('production');
