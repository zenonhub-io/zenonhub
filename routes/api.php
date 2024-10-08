<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Nom\Accelerator;
use App\Http\Controllers\Api\Nom\Bridge;
use App\Http\Controllers\Api\Nom\Htlc;
use App\Http\Controllers\Api\Nom\Ledger;
use App\Http\Controllers\Api\Nom\Liquidity;
use App\Http\Controllers\Api\Nom\Pillars;
use App\Http\Controllers\Api\Nom\Plasma;
use App\Http\Controllers\Api\Nom\Sentinel;
use App\Http\Controllers\Api\Nom\Stake;
use App\Http\Controllers\Api\Nom\Stats;
use App\Http\Controllers\Api\Nom\Swap;
use App\Http\Controllers\Api\Nom\Token;
use App\Http\Controllers\Api\Utilities;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded within the "api" middleware group which includes
| the middleware most often needed by APIs. Build something great!
|
*/

Route::get('/user', fn (Request $request) => $request->user())->middleware(Authenticate::using('sanctum'));

Route::group(['middleware' => ['throttle:60,1']], function () {

    Route::get('utilities/address-from-public-key', [Utilities::class, 'addressFromPublicKey'])->name('api.utilities.address-from-public-key');
    Route::post('utilities/verify-signed-message', [Utilities::class, 'verifySignedMessage'])->name('api.utilities.verify-signed-message');
    Route::get('utilities/account-lp-balances', [Utilities::class, 'accountLpBalances'])->name('api.utilities.account-lp-balances');
    Route::get('utilities/token-supply/{token}/{value?}', [Utilities::class, 'tokenSupply'])->name('api.utilities.token-supply');
    Route::get('utilities/prices', [Utilities::class, 'tokenPrice'])->name('api.utilities.token-price');

    Route::get('nom/accelerator/get-all', [Accelerator::class, 'getAll'])->name('api.accelerator.get-all');
    Route::get('nom/accelerator/get-project-by-id', [Accelerator::class, 'getProjectById'])->name('api.accelerator.get-project-by-id');
    Route::get('nom/accelerator/get-phase-by-id', [Accelerator::class, 'getPhaseById'])->name('api.accelerator.get-phase-by-id');
    Route::get('nom/accelerator/get-pillar-votes', [Accelerator::class, 'getPillarVotes'])->name('api.accelerator.get-pillar-votes');
    Route::get('nom/accelerator/get-vote-breakdown', [Accelerator::class, 'getVoteBreakdown'])->name('api.accelerator.get-vote-breakdown');

    Route::get('nom/bridge/get-bridge-info', [Bridge::class, 'getBridgeInfo'])->name('api.bridge.get-bridge-info');
    Route::get('nom/bridge/get-security-info', [Bridge::class, 'getSecurityInfo'])->name('api.bridge.get-security-info');
    Route::get('nom/bridge/get-orchestrator-info', [Bridge::class, 'getOrchestratorInfo'])->name('api.bridge.get-orchestrator-info');
    Route::get('nom/bridge/get-time-challenges-info', [Bridge::class, 'getTimeChallengesInfo'])->name('api.bridge.get-time-challenges-info');
    Route::get('nom/bridge/get-network-info', [Bridge::class, 'getNetworkInfo'])->name('api.bridge.get-network-info');
    Route::get('nom/bridge/get-all-networks', [Bridge::class, 'getAllNetworks'])->name('api.bridge.get-all-networks');
    Route::get('nom/bridge/get-redeemable-in', [Bridge::class, 'getRedeemableIn'])->name('api.bridge.get-redeemable-in');
    Route::get('nom/bridge/get-confirmations-to-finality', [Bridge::class, 'getConfirmationsToFinality'])->name('api.bridge.get-confirmations-to-finality');
    Route::get('nom/bridge/get-wrap-token-request-by-id', [Bridge::class, 'getWrapTokenRequestById'])->name('api.bridge.get-wrap-token-request-by-id');
    Route::get('nom/bridge/get-all-wrap-token-requests', [Bridge::class, 'getAllWrapTokenRequests'])->name('api.bridge.get-all-wrap-token-requests');
    Route::get('nom/bridge/get-all-wrap-token-requests-by-to-address', [Bridge::class, 'getAllWrapTokenRequestsByToAddress'])->name('api.bridge.get-all-wrap-token-requests-by-to-address');
    Route::get('nom/bridge/get-all-wrap-token-requests-by-to-address-network-class-and-chain-id', [Bridge::class, 'getAllWrapTokenRequestsByToAddressNetworkClassAndChainId'])->name('api.bridge.get-all-wrap-token-requests-by-to-address-network-class-and-chain-id');
    Route::get('nom/bridge/get-all-unsigned-wrap-token-requests', [Bridge::class, 'getAllUnsignedWrapTokenRequests'])->name('api.bridge.get-all-unsigned-wrap-token-requests');
    Route::get('nom/bridge/get-unwrap-token-request-by-hash-and-log', [Bridge::class, 'getUnwrapTokenRequestByHashAndLog'])->name('api.bridge.get-unwrap-token-request-by-hash-and-log');
    Route::get('nom/bridge/get-all-unwrap-token-requests', [Bridge::class, 'getAllUnwrapTokenRequests'])->name('api.bridge.get-all-unwrap-token-requests');
    Route::get('nom/bridge/get-all-unwrap-token-requests-by-to-address', [Bridge::class, 'getAllUnwrapTokenRequestsByToAddress'])->name('api.bridge.get-all-unwrap-token-requests-by-to-address');
    Route::get('nom/bridge/get-fee-token-pair', [Bridge::class, 'getFeeTokenPair'])->name('api.bridge.get-fee-token-pair');

    Route::get('nom/htlc/get-by-id', [Htlc::class, 'getById'])->name('api.htlc.get-by-id');
    Route::get('nom/htlc/get-proxy-unlock-status', [Htlc::class, 'getProxyUnlockStatus'])->name('api.htlc.get-proxy-unlock-status');

    Route::get('nom/ledger/get-frontier-account-block', [Ledger::class, 'getFrontierAccountBlock'])->name('api.ledger.get-frontier-account-block');
    Route::get('nom/ledger/get-unconfirmed-blocks-by-address', [Ledger::class, 'getUnconfirmedBlocksByAddress'])->name('api.ledger.get-unconfirmed-blocks-by-address');
    Route::get('nom/ledger/get-unreceived-blocks-by-address', [Ledger::class, 'getUnreceivedBlocksByAddress'])->name('api.ledger.get-unreceived-blocks-by-address');
    Route::get('nom/ledger/get-account-block-by-hash', [Ledger::class, 'getAccountBlockByHash'])->name('api.ledger.get-account-block-by-hash');
    Route::get('nom/ledger/get-account-blocks-by-height', [Ledger::class, 'getAccountBlocksByHeight'])->name('api.ledger.get-account-blocks-by-height');
    Route::get('nom/ledger/get-account-blocks-by-page', [Ledger::class, 'getAccountBlocksByPage'])->name('api.ledger.get-account-blocks-by-page');
    Route::get('nom/ledger/get-frontier-momentum', [Ledger::class, 'getFrontierMomentum'])->name('api.ledger.get-frontier-momentum');
    Route::get('nom/ledger/get-momentum-before-time', [Ledger::class, 'getMomentumBeforeTime'])->name('api.ledger.get-momentum-before-time');
    Route::get('nom/ledger/get-momentums-by-page', [Ledger::class, 'getMomentumsByPage'])->name('api.ledger.get-momentums-by-page');
    Route::get('nom/ledger/get-momentum-by-hash', [Ledger::class, 'getMomentumByHash'])->name('api.ledger.get-momentum-by-hash');
    Route::get('nom/ledger/get-momentums-by-height', [Ledger::class, 'getMomentumsByHeight'])->name('api.ledger.get-momentums-by-height');
    Route::get('nom/ledger/get-detailed-momentums-by-height', [Ledger::class, 'getDetailedMomentumsByHeight'])->name('api.ledger.get-detailed-momentums-by-height');
    Route::get('nom/ledger/get-account-info-by-address', [Ledger::class, 'getAccountInfoByAddress'])->name('api.ledger.get-account-info-by-address');

    Route::get('nom/liquidity/get-liquidity-info', [Liquidity::class, 'getLiquidityInfo'])->name('api.liquidity.get-liquidity-info');
    Route::get('nom/liquidity/get-security-info', [Liquidity::class, 'getSecurityInfo'])->name('api.liquidity.get-security-info');
    Route::get('nom/liquidity/get-liquidity-stake-entries-by-address', [Liquidity::class, 'getLiquidityStakeEntriesByAddress'])->name('api.liquidity.get-liquidity-stake-entries-by-address');
    Route::get('nom/liquidity/get-uncollected-reward', [Liquidity::class, 'getUncollectedReward'])->name('api.liquidity.get-uncollected-reward');
    Route::get('nom/liquidity/get-frontier-reward-by-page', [Liquidity::class, 'getFrontierRewardByPage'])->name('api.liquidity.get-frontier-reward-by-page');
    Route::get('nom/liquidity/get-time-challenges-info', [Liquidity::class, 'getTimeChallengesInfo'])->name('api.liquidity.get-time-challenges-info');

    Route::get('nom/pillar/get-qsr-registration-cost', [Pillars::class, 'getQsrRegistrationCost'])->name('api.pillar.get-qsr-registration-cost');
    Route::get('nom/pillar/check-name-availability', [Pillars::class, 'checkNameAvailability'])->name('api.pillar.check-name-availability');
    Route::get('nom/pillar/get-all', [Pillars::class, 'getAll'])->name('api.pillar.get-all');
    Route::get('nom/pillar/get-by-owner', [Pillars::class, 'getByOwner'])->name('api.pillar.get-by-owner');
    Route::get('nom/pillar/get-by-name', [Pillars::class, 'getByName'])->name('api.pillar.get-by-name');
    Route::get('nom/pillar/get-delegated-pillar', [Pillars::class, 'getDelegatedPillar'])->name('api.pillar.get-delegated-pillar');
    Route::get('nom/pillar/get-deposited-qsr', [Pillars::class, 'getDepositedQsr'])->name('api.pillar.get-deposited-qsr');
    Route::get('nom/pillar/get-uncollected-reward', [Pillars::class, 'getUncollectedReward'])->name('api.pillar.get-uncollected-reward');
    Route::get('nom/pillar/get-frontier-reward-by-page', [Pillars::class, 'getFrontierRewardByPage'])->name('api.pillar.get-frontier-reward-by-page');

    Route::get('nom/plasma/get', [Plasma::class, 'get'])->name('api.plasma.get');
    Route::get('nom/plasma/get-entries-by-address', [Plasma::class, 'getEntriesByAddress'])->name('api.plasma.get-entries-by-address');
    Route::get('nom/plasma/get-required-po-w-for-account-block', [Plasma::class, 'getRequiredPoWForAccountBlock'])->name('api.plasma.get-required-po-w-for-account-block');

    Route::get('nom/sentinel/get-by-owner', [Sentinel::class, 'getByOwner'])->name('api.sentinel.get-by-owner');
    Route::get('nom/sentinel/get-all-active', [Sentinel::class, 'getAllActive'])->name('api.sentinel.get-all-active');
    Route::get('nom/sentinel/get-deposited-qsr', [Sentinel::class, 'getDepositedQsr'])->name('api.sentinel.get-deposited-qsr');
    Route::get('nom/sentinel/get-uncollected-reward', [Sentinel::class, 'getUncollectedReward'])->name('api.sentinel.get-uncollected-reward');
    Route::get('nom/sentinel/get-frontier-reward-by-page', [Sentinel::class, 'getFrontierRewardByPage'])->name('api.sentinel.get-frontier-reward-by-page');

    Route::get('nom/stake/get-entries-by-address', [Stake::class, 'getEntriesByAddress'])->name('api.stake.get-entries-by-address');
    Route::get('nom/stake/get-uncollected-reward', [Stake::class, 'getUncollectedReward'])->name('api.stake.get-uncollected-reward');
    Route::get('nom/stake/get-frontier-reward-by-page', [Stake::class, 'getFrontierRewardByPage'])->name('api.stake.get-frontier-reward-by-page');

    Route::get('nom/stats/runtime-info', [Stats::class, 'runtimeInfo'])->name('api.stats.runtime-info');
    Route::get('nom/stats/process-info', [Stats::class, 'processInfo'])->name('api.stats.process-info');
    Route::get('nom/stats/sync-info', [Stats::class, 'syncInfo'])->name('api.stats.sync-info');
    Route::get('nom/stats/network-info', [Stats::class, 'networkInfo'])->name('api.stats.network-info');

    Route::get('nom/swap/get-assets-by-key-id-hash', [Swap::class, 'getAssetsByKeyIdHash'])->name('api.swap.get-assets-by-key-id-hash');
    Route::get('nom/swap/get-assets', [Swap::class, 'getAssets'])->name('api.swap.get-assets');
    Route::get('nom/swap/get-legacy-pillars', [Swap::class, 'getLegacyPillars'])->name('api.swap.get-legacy-pillars');

    Route::get('nom/token/get-all', [Token::class, 'getAll'])->name('api.token.get-all');
    Route::get('nom/token/get-by-owner', [Token::class, 'getByOwner'])->name('api.token.get-by-owner');
    Route::get('nom/token/get-by-zts', [Token::class, 'getByZts'])->name('api.token.get-by-zts');

    //    Route::group([], function () {
    //        Route::prefix('accounts')->name('accounts.')->group(function () {
    //            Route::get('/', [\App\Http\Controllers\Api\Accounts::class, 'get'])->name('get');
    //            Route::get('{address}', [\App\Http\Controllers\Api\Accounts::class, 'find'])->name('get');
    //        });
    //
    //        Route::prefix('stakes')->name('stakes.')->group(function () {
    //            Route::get('/', [\App\Http\Controllers\Api\Stakes::class, 'get'])->name('get');
    //            Route::get('{hash}', [\App\Http\Controllers\Api\Stakes::class, 'find'])->name('get');
    //        });
    //    });

});
