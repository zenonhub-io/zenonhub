<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

include 'redirects.php';
include 'auth.php';

//
// Pages

Route::get('/', [\App\Http\Controllers\Home::class, 'show'])->name('home');

Route::get('donate', [\App\Http\Controllers\Donate::class, 'show'])->name('donate');

Route::get('privacy', [\App\Http\Controllers\Privacy::class, 'show'])->name('privacy');

Route::middleware(['throttle:60,1', 'auth', 'verified'])->group(function () {
    Route::get('account', [\App\Http\Controllers\Account\Overview::class, 'show'])->name('account.overview');
    Route::get('account/details', [\App\Http\Controllers\Account\Details::class, 'show'])->name('account.details');
    Route::get('account/favorites', [\App\Http\Controllers\Account\Favorites::class, 'show'])->name('account.favorites');
    Route::get('account/notifications', [\App\Http\Controllers\Account\Notifications::class, 'show'])->name('account.notifications');
    Route::get('account/addresses', [\App\Http\Controllers\Account\Addresses::class, 'show'])->name('account.addresses');
});

Route::get('pillars', [\App\Http\Controllers\Pillar\Pillars::class, 'show'])->name('pillars.overview');
Route::get('pillars/{slug}', [\App\Http\Controllers\Pillar\Pillars::class, 'detail'])->name('pillars.detail');

Route::get('accelerator-z', [\App\Http\Controllers\Accelerator\Projects::class, 'show'])->name('az.overview');
Route::get('accelerator-z/project/{hash}', [\App\Http\Controllers\Accelerator\Projects::class, 'detail'])->name('az.project');
Route::get('accelerator-z/phase/{hash}', [\App\Http\Controllers\Accelerator\Phases::class, 'detail'])->name('az.phase');

Route::get('explorer', [\App\Http\Controllers\Explorer\Explorer::class, 'show'])->name('explorer.overview');
Route::get('explorer/momentums', [\App\Http\Controllers\Explorer\Momentums::class, 'show'])->name('explorer.momentums');
Route::get('explorer/momentum/{hash}', [\App\Http\Controllers\Explorer\Momentums::class, 'detail'])->name('explorer.momentum');
Route::get('explorer/transactions', [\App\Http\Controllers\Explorer\Transactions::class, 'show'])->name('explorer.transactions');
Route::get('explorer/transaction/{hash}', [\App\Http\Controllers\Explorer\Transactions::class, 'detail'])->name('explorer.transaction');
Route::get('explorer/accounts', [\App\Http\Controllers\Explorer\Accounts::class, 'show'])->name('explorer.accounts');
Route::get('explorer/account/{address}', [\App\Http\Controllers\Explorer\Accounts::class, 'detail'])->name('explorer.account');
Route::get('explorer/tokens', [\App\Http\Controllers\Explorer\Tokens::class, 'show'])->name('explorer.tokens');
Route::get('explorer/token/{zts}', [\App\Http\Controllers\Explorer\Tokens::class, 'detail'])->name('explorer.token');
Route::get('explorer/staking', [\App\Http\Controllers\Explorer\Staking::class, 'show'])->name('explorer.staking');
Route::get('explorer/fusions', [\App\Http\Controllers\Explorer\Fusions::class, 'show'])->name('explorer.fusions');

Route::get('stats', [\App\Http\Controllers\Stats\Overview::class, 'show'])->name('stats.overview');
Route::get('stats/nodes', [\App\Http\Controllers\Stats\Nodes::class, 'show'])->name('stats.nodes');
Route::get('stats/accelerator', [\App\Http\Controllers\Stats\Accelerator::class, 'show'])->name('stats.accelerator');
Route::get('stats/bridge', [\App\Http\Controllers\Stats\Bridge::class, 'show'])->name('stats.bridge');

Route::get('tools', [\App\Http\Controllers\Tools\Overview::class, 'show'])->name('tools.overview');
Route::get('tools/plasma-bot', [\App\Http\Controllers\Tools\PlasmaBot::class, 'show'])->name('tools.plasma-bot');
Route::get('tools/api-playground', [\App\Http\Controllers\Tools\ApiPlayground::class, 'show'])->name('tools.api-playground');
Route::get('tools/verify-signature', [\App\Http\Controllers\Tools\VerifySignature::class, 'show'])->name('tools.verify-signature');
Route::get('tools/broadcast-message', [\App\Http\Controllers\Tools\BroadcastMessage::class, 'show'])->name('tools.broadcast-message');

Route::get('services', [\App\Http\Controllers\Services\Overview::class, 'show'])->name('services.overview');
Route::get('services/whale-alerts', [\App\Http\Controllers\Services\WhaleAlerts::class, 'show'])->name('services.whale-alerts');
Route::get('services/bridge-alerts', [\App\Http\Controllers\Services\BridgeAlerts::class, 'show'])->name('services.bridge-alerts');
Route::get('services/public-nodes', [\App\Http\Controllers\Services\PublicNodes::class, 'show'])->name('services.public-nodes');
Route::get('services/plasma-bot', [\App\Http\Controllers\Services\PlasmaBot::class, 'show'])->name('services.plasma-bot');

if (! app()->isProduction()) {
    Route::prefix('utilities')->name('utilities.')->middleware(['throttle:60,1'])->group(function () {
        Route::get('missing-votes', [\App\Http\Controllers\Utilities\MissingVotes::class, 'show']);
    });
}

//
// Sitemap
Route::get('/sitemap.xml', function () {
    $file = storage_path('app/sitemap/sitemap.xml');

    return response()->file($file, [
        'Content-Type' => 'application/xml',
    ]);
})->name('sitemap');
