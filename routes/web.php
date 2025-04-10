<?php

declare(strict_types=1);

use App\Http\Controllers\AcceleratorZ\AcceleratorPhaseController;
use App\Http\Controllers\AcceleratorZ\AcceleratorProjectsController;
use App\Http\Controllers\AdvertisingController;
use App\Http\Controllers\DonateController;
use App\Http\Controllers\Explorer\AccountsController;
use App\Http\Controllers\Explorer\BridgeController;
use App\Http\Controllers\Explorer\MomentumsController;
use App\Http\Controllers\Explorer\OverviewController;
use App\Http\Controllers\Explorer\PlasmaController;
use App\Http\Controllers\Explorer\StakesController;
use App\Http\Controllers\Explorer\TokensController;
use App\Http\Controllers\Explorer\TransactionsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InfoController;
use App\Http\Controllers\Pillars\PillarsController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RobotsTxtController;
use App\Http\Controllers\Services\PublicNodesController;
use App\Http\Controllers\SitemapXmlController;
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

Route::middleware([
    VerifiedIfLoggedInMiddleware::class,
    UserLastSeenMiddleware::class,
])->group(function () {

    Route::get('/', HomeController::class)->name('home');
    Route::get('info', InfoController::class)->name('info');
    Route::get('donate', DonateController::class)->name('donate');
    Route::get('advertise', AdvertisingController::class)->name('advertise');
    Route::get('privacy-policy', PolicyController::class)->name('policy');
    // Route::get('terms-of-service', TermsController::class)->name('terms');

    Route::post('timezone', [TimezoneController::class, 'update'])->name('timezone.update');

    Route::get('pillars/{tab?}', [PillarsController::class, 'index'])->name('pillar.list');
    Route::get('pillar/{slug}/{tab?}', [PillarsController::class, 'show'])->name('pillar.detail');

    // Route::get('sentinels', SentinelsController::class)->name('sentinel.list');
    // Route::get('sentinel/{address}', SentinelsController::class)->name('sentinel.detail');

    Route::get('accelerator-z/{tab?}', [AcceleratorProjectsController::class, 'index'])->name('accelerator-z.list');
    Route::get('accelerator-z/project/{hash}/{tab?}', [AcceleratorProjectsController::class, 'show'])->name('accelerator-z.project.detail');
    Route::get('accelerator-z/phase/{hash}/{tab?}', AcceleratorPhaseController::class)->name('accelerator-z.phase.detail');

    Route::get('explorer', OverviewController::class)->name('explorer.overview');
    Route::get('explorer/momentums', [MomentumsController::class, 'index'])->name('explorer.momentum.list');
    Route::get('explorer/momentum/{hash}/{tab?}', [MomentumsController::class, 'show'])->name('explorer.momentum.detail');
    Route::get('explorer/transactions', [TransactionsController::class, 'index'])->name('explorer.transaction.list');
    Route::get('explorer/transaction/{hash}/{tab?}', [TransactionsController::class, 'show'])->name('explorer.transaction.detail');
    Route::get('explorer/accounts/{tab?}', [AccountsController::class, 'index'])->name('explorer.account.list');
    Route::get('explorer/account/{address}/{tab?}', [AccountsController::class, 'show'])->name('explorer.account.detail');
    Route::get('explorer/tokens/{tab?}', [TokensController::class, 'index'])->name('explorer.token.list');
    Route::get('explorer/token/{zts}/{tab?}', [TokensController::class, 'show'])->name('explorer.token.detail');
    Route::get('explorer/bridge/{tab?}', BridgeController::class)->name('explorer.bridge.list');
    Route::get('explorer/stakes/{tab?}', StakesController::class)->name('explorer.stake.list');
    Route::get('explorer/plasma', PlasmaController::class)->name('explorer.plasma.list');

    Route::get('stats/bridge/{tab?}', BridgeStatsController::class)->name('stats.bridge');
    Route::get('stats/public-nodes/{tab?}', PublicNodesStatsController::class)->name('stats.public-nodes');
    Route::get('stats/accelerator-z/{tab?}', AcceleratorZStatsController::class)->name('stats.accelerator-z');

    Route::get('tools/plasma-bot', PlasmaBotController::class)->name('tools.plasma-bot');
    Route::get('tools/api-playground', ApiPlaygroundController::class)->name('tools.api-playground');
    // Route::get('tools/broadcast-message', HomeController::class)->name('tools.broadcast-message');
    Route::get('tools/verify-signature', VerifySignatureController::class)->name('tools.verify-signature');

    Route::get('services/public-nodes', PublicNodesController::class)->name('services.public-nodes');
});

Route::middleware([
    'auth:sanctum',
    AuthenticateSessionMiddleware::class,
    UserLastSeenMiddleware::class,
])->group(function () {
    Route::get('profile/{tab?}', ProfileController::class)->name('profile');
});

Route::get('robots.txt', RobotsTxtController::class)->name('robots');
Route::get('sitemap.xml', SitemapXmlController::class)->name('sitemap');
