<?php

declare(strict_types=1);

use App\Http\Controllers\AcceleratorPhaseController;
use App\Http\Controllers\AcceleratorProjectController;
use App\Http\Controllers\AcceleratorZController;
use App\Http\Controllers\Explorer\ExplorerBridgeController;
use App\Http\Controllers\Explorer\ExplorerOverviewController;
use App\Http\Controllers\Explorer\ExplorerPlasmaController;
use App\Http\Controllers\Explorer\ExplorerStakesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PillarDetailController;
use App\Http\Controllers\PillarsController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SentinelsController;
use App\Http\Controllers\Stats\AcceleratorZStatsController;
use App\Http\Controllers\Stats\BridgeStatsController;
use App\Http\Controllers\Stats\PublicNodesStatsController;
use App\Http\Controllers\TimezoneController;
use App\Http\Controllers\Tools\ApiPlaygroundController;
use App\Http\Controllers\Tools\PlasmaBotController;
use App\Http\Controllers\Tools\VerifySignatureController;
use App\Http\Middleware\AuthenticateSessionMiddleware;
use App\Http\Middleware\UserLastSeenMiddleware;
use App\Http\Middleware\VerifiedIfLoggedInMiddleware;
use Illuminate\Support\Facades\Route;

include 'redirects.php';

Route::get('test', function () {

    $cli = app(App\Services\ZenonCli\ZenonCli::class);

    dd($cli);

    //$cli->setKeystore(config('services.plasma-bot.keystore'));
    //$cli->setPassphrase(config('services.plasma-bot.passphrase'));

    $wallets = $cli->walletList();

    dd($wallets);

    $cli->walletCreateNew(
        config('services.plasma-bot.keystore'),
        config('services.plasma-bot.passphrase'),
    );

    dd('dome');

    $token = 'Uzj87ixm14JnNXMflMsM0oneRlwEBx7ZfpEzIkk00090759b';
    $response = Http::withToken($token)
        ->accept('application/json')
        ->get('http://zenonhub2.test/api/user')
        ->json();

    dd($response);
});

Route::middleware([
    VerifiedIfLoggedInMiddleware::class,
    UserLastSeenMiddleware::class,
])->group(function () {

    Route::get('/', HomeController::class)->name('home');
    //Route::get('terms-of-service', TermsController::class)->name('terms');
    Route::get('privacy-policy', PolicyController::class)->name('policy');
    Route::get('donate', HomeController::class)->name('donate');
    Route::get('sponsor', HomeController::class)->name('sponsor');

    Route::post('/timezone', [TimezoneController::class, 'update'])->name('timezone.update');

    Route::get('pillars/{tab?}', PillarsController::class)->name('pillar.list');
    Route::get('pillar/{slug}/{tab?}', PillarDetailController::class)->name('pillar.detail');

    Route::get('sentinels', SentinelsController::class)->name('sentinel.list');
    Route::get('sentinel/{address}', SentinelsController::class)->name('sentinel.detail');

    Route::get('accelerator-z/{tab?}', AcceleratorZController::class)->name('accelerator-z.list');
    Route::get('accelerator-z/project/{hash}/{tab?}', AcceleratorProjectController::class)->name('accelerator-z.project.detail');
    Route::get('accelerator-z/phase/{hash}/{tab?}', AcceleratorPhaseController::class)->name('accelerator-z.phase.detail');

    Route::get('explorer', ExplorerOverviewController::class)->name('explorer.overview');
    Route::get('explorer/momentums', HomeController::class)->name('explorer.momentum.list');
    Route::get('explorer/momentum/{hash}/{tab?}', HomeController::class)->name('explorer.momentum.detail');
    Route::get('explorer/transactions', HomeController::class)->name('explorer.transaction.list');
    Route::get('explorer/transaction/{hash}/{tab?}', HomeController::class)->name('explorer.transaction.detail');
    Route::get('explorer/accounts', HomeController::class)->name('explorer.account.list');
    Route::get('explorer/account/{address}/{tab?}', HomeController::class)->name('explorer.account.detail');
    Route::get('explorer/tokens', HomeController::class)->name('explorer.token.list');
    Route::get('explorer/token/{zts}/{tab?}', HomeController::class)->name('explorer.token.detail');
    Route::get('explorer/bridge/{tab?}', ExplorerBridgeController::class)->name('explorer.bridge.list');
    Route::get('explorer/stakes', ExplorerStakesController::class)->name('explorer.stake.list');
    Route::get('explorer/plasma', ExplorerPlasmaController::class)->name('explorer.plasma.list');

    Route::get('stats/bridge/{tab?}', BridgeStatsController::class)->name('stats.bridge');
    Route::get('stats/public-nodes/{tab?}', PublicNodesStatsController::class)->name('stats.public-nodes');
    Route::get('stats/accelerator-z/{tab?}', AcceleratorZStatsController::class)->name('stats.accelerator-z');

    Route::get('tools/plasma-bot', PlasmaBotController::class)->name('tools.plasma-bot');
    Route::get('tools/api-playground', ApiPlaygroundController::class)->name('tools.api-playground');
    //Route::get('tools/broadcast-message', HomeController::class)->name('tools.broadcast-message');
    Route::get('tools/verify-signature', VerifySignatureController::class)->name('tools.verify-signature');

    Route::get('services/public-nodes', HomeController::class)->name('services.public-nodes');
    Route::get('services/plasma-bot', HomeController::class)->name('services.plasma-bot');
    Route::get('services/whale-alerts', HomeController::class)->name('services.whale-alerts');
    Route::get('services/bridge-alerts', HomeController::class)->name('services.bridge-alerts');
});

Route::middleware([
    'auth:sanctum',
    AuthenticateSessionMiddleware::class,
    UserLastSeenMiddleware::class,
])->group(function () {
    Route::get('profile/{tab?}', ProfileController::class)->name('profile');
});

Route::get('sitemap.xml', function () {
    $file = storage_path('app/sitemap/sitemap.xml');

    return response()->file($file, [
        'Content-Type' => 'application/xml',
    ]);
})->name('sitemap');
