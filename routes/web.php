<?php

declare(strict_types=1);

use App\Http\Controllers\AcceleratorZController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PillarsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SentinelsController;
use App\Http\Middleware\AuthenticateSessionMiddleware;
use App\Http\Middleware\UserLastSeenMiddleware;
use App\Http\Middleware\VerifiedIfLoggedInMiddleware;
use Illuminate\Support\Facades\Route;

include 'redirects.php';

Route::get('test', function () {

    dd(sprintf(
        '%s%s%s',
        '',
        $tokenEntropy = Illuminate\Support\Str::random(40),
        hash('crc32b', $tokenEntropy)
    ));

    $account = App\Domains\Nom\Models\Account::find(13852);
    //App\Domains\Nom\Actions\UpdateAccountTotals::run($account);

    $sent = Illuminate\Support\Facades\DB::table('nom_account_blocks')
        ->selectRaw('CAST(SUM(amount) AS INTEGER) as total')
        ->where('account_id', $account->id)
        ->where('token_id', 2)
        ->first()->total;

    $received = Illuminate\Support\Facades\DB::table('nom_account_blocks')
        ->selectRaw('CAST(SUM(amount) AS INTEGER) as total')
        ->where('to_account_id', $account->id)
        ->where('token_id', 2)
        ->first()->total;

    $balance = ($received - $sent);

    $qsrBalance = App\Domains\Nom\Models\Token::find(2)?->getDisplayAmount($balance);

    dd($sent, $received, $qsrBalance);

    dd('complete');

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
    Route::get('terms-of-service', HomeController::class)->name('terms');
    Route::get('privacy-policy', HomeController::class)->name('policy');
    Route::get('donate', HomeController::class)->name('donate');
    Route::get('sponsor', HomeController::class)->name('sponsor');

    Route::get('pillars', PillarsController::class)->name('pillars');
    Route::get('pillar/{slug}', PillarsController::class)->name('pillar.detail');

    Route::get('sentinels', SentinelsController::class)->name('sentinels');
    Route::get('sentinel/{address}', SentinelsController::class)->name('sentinel.detail');

    Route::get('accelerator-z', AcceleratorZController::class)->name('accelerator-z');
    Route::get('accelerator-z/project/{hash}', AcceleratorZController::class)->name('accelerator-z.project.detail');
    Route::get('accelerator-z/phase/{hash}', AcceleratorZController::class)->name('accelerator-z.phase.detail');

    Route::get('explorer', HomeController::class)->name('explorer');
    Route::get('explorer/momentums', HomeController::class)->name('explorer.momentums');
    Route::get('explorer/momentum/{hash}', HomeController::class)->name('explorer.momentum.detail');
    Route::get('explorer/transactions', HomeController::class)->name('explorer.transactions');
    Route::get('explorer/transaction/{hash}', HomeController::class)->name('explorer.transaction.detail');
    Route::get('explorer/accounts', HomeController::class)->name('explorer.accounts');
    Route::get('explorer/account/{address}', HomeController::class)->name('explorer.account.detail');
    Route::get('explorer/tokens', HomeController::class)->name('explorer.tokens');
    Route::get('explorer/token/{zts}', HomeController::class)->name('explorer.token.detail');
    Route::get('explorer/bridge', HomeController::class)->name('explorer.bridge');
    Route::get('explorer/stakes', HomeController::class)->name('explorer.stakes');
    Route::get('explorer/plasma', HomeController::class)->name('explorer.plasma');

    Route::get('stats/bridge', HomeController::class)->name('stats.bridge');
    Route::get('stats/public-nodes', HomeController::class)->name('stats.public-nodes');
    Route::get('stats/accelerator-z', HomeController::class)->name('stats.accelerator-z');

    Route::get('tools/plasma-bot', HomeController::class)->name('tools.plasma-bot');
    Route::get('tools/api-playground', HomeController::class)->name('tools.api-playground');
    Route::get('tools/broadcast-message', HomeController::class)->name('tools.broadcast-message');
    Route::get('tools/verify-signature', HomeController::class)->name('tools.verify-signature');

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
