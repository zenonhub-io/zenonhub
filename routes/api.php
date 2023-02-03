<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group(['middleware' => ['throttle:60,1']], function () {

    Route::prefix('nom')->group(function (){
        Route::prefix('accelerator')->name('Accelerator.')->group(function () {
            Route::get('get-all', [App\Http\Controllers\Api\Accelerator::class, 'getAll'])->name('getAll');
            Route::get('get-project-by-id', [App\Http\Controllers\Api\Accelerator::class, 'getProjectById'])->name('getProjectById');
            Route::get('get-phase-by-id', [App\Http\Controllers\Api\Accelerator::class, 'getPhaseById'])->name('getPhaseById');
            Route::get('get-pillar-votes', [App\Http\Controllers\Api\Accelerator::class, 'getPillarVotes'])->name('getPillarVotes');
            Route::get('get-vote-breakdown', [App\Http\Controllers\Api\Accelerator::class, 'getPillarVotes'])->name('getPillarVotes');
        });

        Route::prefix('ledger')->name('Ledger.')->group(function () {
            Route::get('get-frontier-account-block', [App\Http\Controllers\Api\Ledger::class, 'getFrontierAccountBlock'])->name('getFrontierAccountBlock');
            Route::get('get-unconfirmed-blocks-by-address', [App\Http\Controllers\Api\Ledger::class, 'getUnconfirmedBlocksByAddress'])->name('getUnconfirmedBlocksByAddress');
            Route::get('get-unreceived-blocks-by-address', [App\Http\Controllers\Api\Ledger::class, 'getUnreceivedBlocksByAddress'])->name('getUnreceivedBlocksByAddress');
            Route::get('get-account-block-by-hash', [App\Http\Controllers\Api\Ledger::class, 'getAccountBlockByHash'])->name('getAccountBlockByHash');
            Route::get('get-account-blocks-by-height', [App\Http\Controllers\Api\Ledger::class, 'getAccountBlocksByHeight'])->name('getAccountBlocksByHeight');
            Route::get('get-account-blocks-by-page', [App\Http\Controllers\Api\Ledger::class, 'getAccountBlocksByPage'])->name('getAccountBlocksByPage');
            Route::get('get-frontier-momentum', [App\Http\Controllers\Api\Ledger::class, 'getFrontierMomentum'])->name('getFrontierMomentum');
            Route::get('get-momentum-before-time', [App\Http\Controllers\Api\Ledger::class, 'getMomentumBeforeTime'])->name('getMomentumBeforeTime');
            Route::get('get-momentums-by-page', [App\Http\Controllers\Api\Ledger::class, 'getMomentumsByPage'])->name('getMomentumsByPage');
            Route::get('get-momentum-by-hash', [App\Http\Controllers\Api\Ledger::class, 'getMomentumByHash'])->name('getMomentumByHash');
            Route::get('get-momentums-by-height', [App\Http\Controllers\Api\Ledger::class, 'getMomentumsByHeight'])->name('getMomentumsByHeight');
            Route::get('get-detailed-momentums-by-height', [App\Http\Controllers\Api\Ledger::class, 'getDetailedMomentumsByHeight'])->name('getDetailedMomentumsByHeight');
            Route::get('get-account-info-by-address', [App\Http\Controllers\Api\Ledger::class, 'getAccountInfoByAddress'])->name('getAccountInfoByAddress');
        });

        Route::prefix('pillars')->name('Pillar.')->group(function () {
            Route::get('get-qsr-registration-cost', [App\Http\Controllers\Api\Pillars::class, 'getQsrRegistrationCost'])->name('getQsrRegistrationCost');
            Route::get('check-name-availability', [App\Http\Controllers\Api\Pillars::class, 'checkNameAvailability'])->name('checkNameAvailability');
            Route::get('get-all', [App\Http\Controllers\Api\Pillars::class, 'getAll'])->name('getAll');
            Route::get('get-by-owner', [App\Http\Controllers\Api\Pillars::class, 'getByOwner'])->name('getByOwner');
            Route::get('get-by-name', [App\Http\Controllers\Api\Pillars::class, 'getByName'])->name('getByName');
            Route::get('get-delegated-pillar', [App\Http\Controllers\Api\Pillars::class, 'getDelegatedPillar'])->name('getDelegatedPillar');
            Route::get('get-deposited-qsr', [App\Http\Controllers\Api\Pillars::class, 'getDepositedQsr'])->name('getDepositedQsr');
            Route::get('get-uncollected-reward', [App\Http\Controllers\Api\Pillars::class, 'getUncollectedReward'])->name('getUncollectedReward');
            Route::get('get-frontier-reward-by-page', [App\Http\Controllers\Api\Pillars::class, 'getFrontierRewardByPage'])->name('getFrontierRewardByPage');
        });

        Route::prefix('plasma')->name('Plasma.')->group(function () {
            Route::get('get', [App\Http\Controllers\Api\Plasma::class, 'get'])->name('get');
            Route::get('get-entries-by-address', [App\Http\Controllers\Api\Plasma::class, 'getEntriesByAddress'])->name('getEntriesByAddress');
            Route::get('get-required-po-w-for-account-block', [App\Http\Controllers\Api\Plasma::class, 'getRequiredPoWForAccountBlock'])->name('getRequiredPoWForAccountBlock');
        });

        Route::prefix('sentinel')->name('Sentinel.')->group(function () {
            Route::get('get-by-owner', [App\Http\Controllers\Api\Sentinel::class, 'getByOwner'])->name('getByOwner');
            Route::get('get-all-active', [App\Http\Controllers\Api\Sentinel::class, 'getAllActive'])->name('getAllActive');
            Route::get('get-deposited-qsr', [App\Http\Controllers\Api\Sentinel::class, 'getDepositedQsr'])->name('getDepositedQsr');
            Route::get('get-uncollected-reward', [App\Http\Controllers\Api\Sentinel::class, 'getUncollectedReward'])->name('getUncollectedReward');
            Route::get('get-frontier-reward-by-page', [App\Http\Controllers\Api\Sentinel::class, 'getFrontierRewardByPage'])->name('getFrontierRewardByPage');
        });

        Route::prefix('stake')->name('Stake.')->group(function () {
            Route::get('get-entries-by-address', [App\Http\Controllers\Api\Stake::class, 'getEntriesByAddress'])->name('getEntriesByAddress');
            Route::get('get-uncollected-reward', [App\Http\Controllers\Api\Stake::class, 'getUncollectedReward'])->name('getUncollectedReward');
            Route::get('get-frontier-reward-by-page', [App\Http\Controllers\Api\Stake::class, 'getFrontierRewardByPage'])->name('getFrontierRewardByPage');
        });

        Route::prefix('stats')->name('Stats.')->group(function () {
            Route::get('os-info', [App\Http\Controllers\Api\Stats::class, 'osInfo'])->name('osInfo');
            Route::get('runtime-info', [App\Http\Controllers\Api\Stats::class, 'runtimeInfo'])->name('runtimeInfo');
            Route::get('process-info', [App\Http\Controllers\Api\Stats::class, 'processInfo'])->name('processInfo');
            Route::get('sync-info', [App\Http\Controllers\Api\Stats::class, 'syncInfo'])->name('syncInfo');
            Route::get('network-info', [App\Http\Controllers\Api\Stats::class, 'networkInfo'])->name('networkInfo');
        });

        Route::prefix('swap')->name('Swap.')->group(function () {
            Route::get('get-assets-by-key-id-hash', [App\Http\Controllers\Api\Swap::class, 'getAssetsByKeyIdHash'])->name('getAssetsByKeyIdHash');
            Route::get('get-assets', [App\Http\Controllers\Api\Swap::class, 'getAssets'])->name('getAssets');
            Route::get('get-legacy-pillars', [App\Http\Controllers\Api\Swap::class, 'getLegacyPillars'])->name('getLegacyPillars');
        });

        Route::prefix('token')->name('Token.')->group(function () {
            Route::get('get-all', [App\Http\Controllers\Api\Token::class, 'getAll'])->name('getAll');
            Route::get('get-by-owner', [App\Http\Controllers\Api\Token::class, 'getByOwner'])->name('getByOwner');
            Route::get('get-by-zts', [App\Http\Controllers\Api\Token::class, 'getByZts'])->name('getByZts');
        });
    });

    Route::prefix('utilities')->name('Utilities.')->group(function () {
        Route::get('address-from-public-key', [App\Http\Controllers\Api\Utilities::class, 'addressFromPublicKey'])->name('addressFromPublicKey');
        Route::post('verify-signed-message', [App\Http\Controllers\Api\Utilities::class, 'verifySignedMessage'])->name('verifySignedMessage');
    });
});
