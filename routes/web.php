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

Route::prefix('account')->name('account.')->middleware(['throttle:60,1', 'auth', 'verified'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Account\Overview::class, 'show'])->name('overview');
    Route::get('details', [\App\Http\Controllers\Account\Details::class, 'show'])->name('details');
    Route::get('favorites', [\App\Http\Controllers\Account\Favorites::class, 'show'])->name('favorites');
    Route::get('notifications', [\App\Http\Controllers\Account\Notifications::class, 'show'])->name('notifications');
    Route::get('addresses', [\App\Http\Controllers\Account\Addresses::class, 'show'])->name('addresses');
});

Route::prefix('pillars')->name('pillars.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Pillar\Pillars::class, 'show'])->name('overview');
    Route::get('/{slug}', [\App\Http\Controllers\Pillar\Pillars::class, 'detail'])->name('detail');
});

Route::prefix('accelerator-z')->name('az.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Accelerator\Projects::class, 'show'])->name('overview');
    Route::get('project/{hash}', [\App\Http\Controllers\Accelerator\Projects::class, 'detail'])->name('project');
    Route::get('phase/{hash}', [\App\Http\Controllers\Accelerator\Phases::class, 'detail'])->name('phase');
});

Route::prefix('explorer')->name('explorer.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Explorer\Explorer::class, 'show'])->name('overview');

    Route::get('momentums/', [\App\Http\Controllers\Explorer\Momentums::class, 'show'])->name('momentums');
    Route::get('momentum/{hash}', [\App\Http\Controllers\Explorer\Momentums::class, 'detail'])->name('momentum');

    Route::get('transactions/', [\App\Http\Controllers\Explorer\Transactions::class, 'show'])->name('transactions');
    Route::get('transaction/{hash}', [\App\Http\Controllers\Explorer\Transactions::class, 'detail'])->name('transaction');

    Route::get('accounts/', [\App\Http\Controllers\Explorer\Accounts::class, 'show'])->name('accounts');
    Route::get('account/{address}', [\App\Http\Controllers\Explorer\Accounts::class, 'detail'])->name('account');

    Route::get('tokens/', [\App\Http\Controllers\Explorer\Tokens::class, 'show'])->name('tokens');
    Route::get('token/{zts}', [\App\Http\Controllers\Explorer\Tokens::class, 'detail'])->name('token');

    Route::get('staking/', [\App\Http\Controllers\Explorer\Staking::class, 'show'])->name('staking');

    Route::get('fusions/', [\App\Http\Controllers\Explorer\Fusions::class, 'show'])->name('fusions');
});

Route::prefix('stats')->name('stats.')->middleware(['throttle:60,1'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Stats\Overview::class, 'show'])->name('overview');
    Route::get('nodes', [\App\Http\Controllers\Stats\Nodes::class, 'show'])->name('nodes');
    Route::get('accelerator', [\App\Http\Controllers\Stats\Accelerator::class, 'show'])->name('accelerator');
    Route::get('bridge', [\App\Http\Controllers\Stats\Bridge::class, 'show'])->name('bridge');
});

Route::prefix('tools')->name('tools.')->middleware(['throttle:60,1'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Tools\Overview::class, 'show'])->name('overview');
    Route::get('plasma-bot', [\App\Http\Controllers\Tools\PlasmaBot::class, 'show'])->name('plasma-bot');
    Route::get('api-playground', [\App\Http\Controllers\Tools\ApiPlayground::class, 'show'])->name('api-playground');
    Route::get('verify-signature', [\App\Http\Controllers\Tools\VerifySignature::class, 'show'])->name('verify-signature');
    Route::get('broadcast-message', [\App\Http\Controllers\Tools\BroadcastMessage::class, 'show'])->name('broadcast-message');
});

Route::prefix('services')->name('services.')->middleware(['throttle:60,1'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Services\Overview::class, 'show'])->name('overview');
    Route::get('whale-alerts', [\App\Http\Controllers\Services\WhaleAlerts::class, 'show'])->name('whale-alerts');
    Route::get('bridge-alerts', [\App\Http\Controllers\Services\BridgeAlerts::class, 'show'])->name('bridge-alerts');
    Route::get('public-nodes', [\App\Http\Controllers\Services\PublicNodes::class, 'show'])->name('public-nodes');
    Route::get('plasma-bot', [\App\Http\Controllers\Services\PlasmaBot::class, 'show'])->name('plasma-bot');
});

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
