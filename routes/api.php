<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Nom\AcceleratorController;
use App\Http\Controllers\Api\Nom\BridgeController;
use App\Http\Controllers\Api\Nom\HtlcController;
use App\Http\Controllers\Api\Nom\LedgerController;
use App\Http\Controllers\Api\Nom\LiquidityController;
use App\Http\Controllers\Api\Nom\PillarsController;
use App\Http\Controllers\Api\Nom\PlasmaController;
use App\Http\Controllers\Api\Nom\SentinelController;
use App\Http\Controllers\Api\Nom\StakeController;
use App\Http\Controllers\Api\Nom\StatsController;
use App\Http\Controllers\Api\Nom\SwapController;
use App\Http\Controllers\Api\Nom\TokenController;
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
    Route::get('utilities/zts-from-hash', [Utilities::class, 'ztsFromHash'])->name('api.utilities.zts-from-hash');
    Route::post('utilities/verify-signed-message', [Utilities::class, 'verifySignedMessage'])->name('api.utilities.verify-signed-message');

    Route::get('utilities/account-lp-balances', [Utilities::class, 'accountLpBalances'])->name('api.utilities.account-lp-balances');
    Route::get('utilities/token-supply/{token}/{value?}', [Utilities::class, 'tokenSupply'])->name('api.utilities.token-supply');
    Route::get('utilities/prices', [Utilities::class, 'tokenPrice'])->name('api.utilities.token-price');

    Route::get('reward-totals', [Utilities::class, 'rewardTotals'])->name('rewardTotals');
    Route::post('plasma-bot/fuse', [Utilities::class, 'plasmaBotFuse'])->name('plasmaBot.fuse');
    Route::get('plasma-bot/expiration/{address}', [Utilities::class, 'plasmaBotExpiration'])->name('plasmaBot.expiration');

    Route::get('tx-stats', function () {

        $start = now()->subDays(15)->startOfDay()->format('Y-m-d H:i:s');
        $end = now()->subDay()->endOfDay()->format('Y-m-d H:i:s');
        $cacheKey = 'tx-stats-' . $start . '-' . $end;

        return Cache::rememberForever($cacheKey, function () use ($start, $end) {
            $results = [];
            $contractMethods = App\Models\Nom\ContractMethod::get();
            $contractMethodIds = $contractMethods->pluck('id');
            $contractTxs = App\Models\Nom\AccountBlock::selectRaw('DATE(created_at) as date, COUNT(*) as totalTx, nom_contracts.name as contract_name, nom_contract_methods.name as contract_method_name')
                ->leftJoin('nom_contract_methods', 'nom_contract_methods.id', '=', 'nom_account_blocks.contract_method_id')
                ->leftJoin('nom_contracts', 'nom_contracts.id', '=', 'nom_contract_methods.contract_id')
                ->whereIn('contract_method_id', $contractMethodIds)
                ->whereBetween('created_at', [$start, $end])
                ->groupBy('date', 'contract_method_id')
                ->get();

            $contractTxs->each(function ($statistic) use (&$results) {

                if (! $statistic->totalTx) {
                    return;
                }

                $arrayKey = $statistic->contract_name . '.' . $statistic->contract_method_name;
                $results[$statistic->date]['contracts'][$arrayKey] = $statistic->totalTx;
            });

            $normalTxs = App\Models\Nom\AccountBlock::selectRaw('DATE(created_at) as date, COUNT(*) as totalTx, block_type')
                ->whereNull('contract_method_id')
                ->whereBetween('created_at', [$start, $end])
                ->groupBy('date', 'block_type')
                ->get();

            $normalTxs->each(function ($statistic) use (&$results) {

                if (! $statistic->totalTx) {
                    return;
                }

                $arrayKey = 'Genesis';

                if ($statistic->block_type === 2) {
                    $arrayKey = 'Send';
                }

                if ($statistic->block_type === 3) {
                    $arrayKey = 'Receive';
                }

                if ($statistic->block_type === 4) {
                    $arrayKey = 'ContractSend';
                }

                if ($statistic->block_type === 5) {
                    $arrayKey = 'ContractReceive';
                }

                $results[$statistic->date][$arrayKey] = $statistic->totalTx;
            });

            return json_encode($results);
        });
    });

    Route::get('nom/accelerator/get-all', [AcceleratorController::class, 'getAll'])->name('api.accelerator.get-all');
    Route::get('nom/accelerator/get-project-by-id', [AcceleratorController::class, 'getProjectById'])->name('api.accelerator.get-project-by-id');
    Route::get('nom/accelerator/get-phase-by-id', [AcceleratorController::class, 'getPhaseById'])->name('api.accelerator.get-phase-by-id');
    Route::get('nom/accelerator/get-pillar-votes', [AcceleratorController::class, 'getPillarVotes'])->name('api.accelerator.get-pillar-votes');
    Route::get('nom/accelerator/get-vote-breakdown', [AcceleratorController::class, 'getVoteBreakdown'])->name('api.accelerator.get-vote-breakdown');

    Route::get('nom/bridge/get-bridge-info', [BridgeController::class, 'getBridgeInfo'])->name('api.bridge.get-bridge-info');
    Route::get('nom/bridge/get-security-info', [BridgeController::class, 'getSecurityInfo'])->name('api.bridge.get-security-info');
    Route::get('nom/bridge/get-orchestrator-info', [BridgeController::class, 'getOrchestratorInfo'])->name('api.bridge.get-orchestrator-info');
    Route::get('nom/bridge/get-time-challenges-info', [BridgeController::class, 'getTimeChallengesInfo'])->name('api.bridge.get-time-challenges-info');
    Route::get('nom/bridge/get-network-info', [BridgeController::class, 'getNetworkInfo'])->name('api.bridge.get-network-info');
    Route::get('nom/bridge/get-all-networks', [BridgeController::class, 'getAllNetworks'])->name('api.bridge.get-all-networks');
    Route::get('nom/bridge/get-redeemable-in', [BridgeController::class, 'getRedeemableIn'])->name('api.bridge.get-redeemable-in');
    Route::get('nom/bridge/get-confirmations-to-finality', [BridgeController::class, 'getConfirmationsToFinality'])->name('api.bridge.get-confirmations-to-finality');
    Route::get('nom/bridge/get-wrap-token-request-by-id', [BridgeController::class, 'getWrapTokenRequestById'])->name('api.bridge.get-wrap-token-request-by-id');
    Route::get('nom/bridge/get-all-wrap-token-requests', [BridgeController::class, 'getAllWrapTokenRequests'])->name('api.bridge.get-all-wrap-token-requests');
    Route::get('nom/bridge/get-all-wrap-token-requests-by-to-address', [BridgeController::class, 'getAllWrapTokenRequestsByToAddress'])->name('api.bridge.get-all-wrap-token-requests-by-to-address');
    Route::get('nom/bridge/get-all-wrap-token-requests-by-to-address-network-class-and-chain-id', [BridgeController::class, 'getAllWrapTokenRequestsByToAddressNetworkClassAndChainId'])->name('api.bridge.get-all-wrap-token-requests-by-to-address-network-class-and-chain-id');
    Route::get('nom/bridge/get-all-unsigned-wrap-token-requests', [BridgeController::class, 'getAllUnsignedWrapTokenRequests'])->name('api.bridge.get-all-unsigned-wrap-token-requests');
    Route::get('nom/bridge/get-unwrap-token-request-by-hash-and-log', [BridgeController::class, 'getUnwrapTokenRequestByHashAndLog'])->name('api.bridge.get-unwrap-token-request-by-hash-and-log');
    Route::get('nom/bridge/get-all-unwrap-token-requests', [BridgeController::class, 'getAllUnwrapTokenRequests'])->name('api.bridge.get-all-unwrap-token-requests');
    Route::get('nom/bridge/get-all-unwrap-token-requests-by-to-address', [BridgeController::class, 'getAllUnwrapTokenRequestsByToAddress'])->name('api.bridge.get-all-unwrap-token-requests-by-to-address');
    Route::get('nom/bridge/get-fee-token-pair', [BridgeController::class, 'getFeeTokenPair'])->name('api.bridge.get-fee-token-pair');

    Route::get('nom/htlc/get-by-id', [HtlcController::class, 'getById'])->name('api.htlc.get-by-id');
    Route::get('nom/htlc/get-proxy-unlock-status', [HtlcController::class, 'getProxyUnlockStatus'])->name('api.htlc.get-proxy-unlock-status');

    Route::get('nom/ledger/get-frontier-account-block', [LedgerController::class, 'getFrontierAccountBlock'])->name('api.ledger.get-frontier-account-block');
    Route::get('nom/ledger/get-unconfirmed-blocks-by-address', [LedgerController::class, 'getUnconfirmedBlocksByAddress'])->name('api.ledger.get-unconfirmed-blocks-by-address');
    Route::get('nom/ledger/get-unreceived-blocks-by-address', [LedgerController::class, 'getUnreceivedBlocksByAddress'])->name('api.ledger.get-unreceived-blocks-by-address');
    Route::get('nom/ledger/get-account-block-by-hash', [LedgerController::class, 'getAccountBlockByHash'])->name('api.ledger.get-account-block-by-hash');
    Route::get('nom/ledger/get-account-blocks-by-height', [LedgerController::class, 'getAccountBlocksByHeight'])->name('api.ledger.get-account-blocks-by-height');
    Route::get('nom/ledger/get-account-blocks-by-page', [LedgerController::class, 'getAccountBlocksByPage'])->name('api.ledger.get-account-blocks-by-page');
    Route::get('nom/ledger/get-frontier-momentum', [LedgerController::class, 'getFrontierMomentum'])->name('api.ledger.get-frontier-momentum');
    Route::get('nom/ledger/get-momentum-before-time', [LedgerController::class, 'getMomentumBeforeTime'])->name('api.ledger.get-momentum-before-time');
    Route::get('nom/ledger/get-momentums-by-page', [LedgerController::class, 'getMomentumsByPage'])->name('api.ledger.get-momentums-by-page');
    Route::get('nom/ledger/get-momentum-by-hash', [LedgerController::class, 'getMomentumByHash'])->name('api.ledger.get-momentum-by-hash');
    Route::get('nom/ledger/get-momentums-by-height', [LedgerController::class, 'getMomentumsByHeight'])->name('api.ledger.get-momentums-by-height');
    Route::get('nom/ledger/get-detailed-momentums-by-height', [LedgerController::class, 'getDetailedMomentumsByHeight'])->name('api.ledger.get-detailed-momentums-by-height');
    Route::get('nom/ledger/get-account-info-by-address', [LedgerController::class, 'getAccountInfoByAddress'])->name('api.ledger.get-account-info-by-address');

    Route::get('nom/liquidity/get-liquidity-info', [LiquidityController::class, 'getLiquidityInfo'])->name('api.liquidity.get-liquidity-info');
    Route::get('nom/liquidity/get-security-info', [LiquidityController::class, 'getSecurityInfo'])->name('api.liquidity.get-security-info');
    Route::get('nom/liquidity/get-liquidity-stake-entries-by-address', [LiquidityController::class, 'getLiquidityStakeEntriesByAddress'])->name('api.liquidity.get-liquidity-stake-entries-by-address');
    Route::get('nom/liquidity/get-uncollected-reward', [LiquidityController::class, 'getUncollectedReward'])->name('api.liquidity.get-uncollected-reward');
    Route::get('nom/liquidity/get-frontier-reward-by-page', [LiquidityController::class, 'getFrontierRewardByPage'])->name('api.liquidity.get-frontier-reward-by-page');
    Route::get('nom/liquidity/get-time-challenges-info', [LiquidityController::class, 'getTimeChallengesInfo'])->name('api.liquidity.get-time-challenges-info');

    Route::get('nom/pillar/get-qsr-registration-cost', [PillarsController::class, 'getQsrRegistrationCost'])->name('api.pillar.get-qsr-registration-cost');
    Route::get('nom/pillar/check-name-availability', [PillarsController::class, 'checkNameAvailability'])->name('api.pillar.check-name-availability');
    Route::get('nom/pillar/get-all', [PillarsController::class, 'getAll'])->name('api.pillar.get-all');
    Route::get('nom/pillar/get-by-owner', [PillarsController::class, 'getByOwner'])->name('api.pillar.get-by-owner');
    Route::get('nom/pillar/get-by-name', [PillarsController::class, 'getByName'])->name('api.pillar.get-by-name');
    Route::get('nom/pillar/get-delegated-pillar', [PillarsController::class, 'getDelegatedPillar'])->name('api.pillar.get-delegated-pillar');
    Route::get('nom/pillar/get-deposited-qsr', [PillarsController::class, 'getDepositedQsr'])->name('api.pillar.get-deposited-qsr');
    Route::get('nom/pillar/get-uncollected-reward', [PillarsController::class, 'getUncollectedReward'])->name('api.pillar.get-uncollected-reward');
    Route::get('nom/pillar/get-frontier-reward-by-page', [PillarsController::class, 'getFrontierRewardByPage'])->name('api.pillar.get-frontier-reward-by-page');

    Route::get('nom/plasma/get', [PlasmaController::class, 'get'])->name('api.plasma.get');
    Route::get('nom/plasma/get-entries-by-address', [PlasmaController::class, 'getEntriesByAddress'])->name('api.plasma.get-entries-by-address');
    Route::get('nom/plasma/get-required-po-w-for-account-block', [PlasmaController::class, 'getRequiredPoWForAccountBlock'])->name('api.plasma.get-required-po-w-for-account-block');

    Route::get('nom/sentinel/get-by-owner', [SentinelController::class, 'getByOwner'])->name('api.sentinel.get-by-owner');
    Route::get('nom/sentinel/get-all-active', [SentinelController::class, 'getAllActive'])->name('api.sentinel.get-all-active');
    Route::get('nom/sentinel/get-deposited-qsr', [SentinelController::class, 'getDepositedQsr'])->name('api.sentinel.get-deposited-qsr');
    Route::get('nom/sentinel/get-uncollected-reward', [SentinelController::class, 'getUncollectedReward'])->name('api.sentinel.get-uncollected-reward');
    Route::get('nom/sentinel/get-frontier-reward-by-page', [SentinelController::class, 'getFrontierRewardByPage'])->name('api.sentinel.get-frontier-reward-by-page');

    Route::get('nom/stake/get-entries-by-address', [StakeController::class, 'getEntriesByAddress'])->name('api.stake.get-entries-by-address');
    Route::get('nom/stake/get-uncollected-reward', [StakeController::class, 'getUncollectedReward'])->name('api.stake.get-uncollected-reward');
    Route::get('nom/stake/get-frontier-reward-by-page', [StakeController::class, 'getFrontierRewardByPage'])->name('api.stake.get-frontier-reward-by-page');

    Route::get('nom/stats/runtime-info', [StatsController::class, 'runtimeInfo'])->name('api.stats.runtime-info');
    Route::get('nom/stats/process-info', [StatsController::class, 'processInfo'])->name('api.stats.process-info');
    Route::get('nom/stats/sync-info', [StatsController::class, 'syncInfo'])->name('api.stats.sync-info');
    Route::get('nom/stats/network-info', [StatsController::class, 'networkInfo'])->name('api.stats.network-info');

    Route::get('nom/swap/get-assets-by-key-id-hash', [SwapController::class, 'getAssetsByKeyIdHash'])->name('api.swap.get-assets-by-key-id-hash');
    Route::get('nom/swap/get-assets', [SwapController::class, 'getAssets'])->name('api.swap.get-assets');
    Route::get('nom/swap/get-legacy-pillars', [SwapController::class, 'getLegacyPillars'])->name('api.swap.get-legacy-pillars');

    Route::get('nom/token/get-all', [TokenController::class, 'getAll'])->name('api.token.get-all');
    Route::get('nom/token/get-by-owner', [TokenController::class, 'getByOwner'])->name('api.token.get-by-owner');
    Route::get('nom/token/get-by-zts', [TokenController::class, 'getByZts'])->name('api.token.get-by-zts');

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
