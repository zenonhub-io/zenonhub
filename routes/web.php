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

    $network = App\Domains\Nom\Models\BridgeNetwork::findByNetworkChain('2', '1');
    $token = $network?->tokens()
        ->wherePivot('token_address', '0xb2e96a63479C2Edd2FD62b382c89D5CA79f572d3')
        ->first();

    dd($token);

    //    $contract = \App\Domains\Nom\Models\Contract::firstWhere('name', 'Bridge');
    //    \App\Domains\Indexer\Actions\IndexContract::run($contract);
    //
    ////    $accountBlocks = App\Domains\Nom\Models\AccountBlock::with('contractMethod', 'data')
    ////        ->where('contract_method_id', 12)
    ////        ->get();
    ////    $accountBlocks->each(function ($accountBlock) {
    ////        $blockProcessorClass = App\Domains\Indexer\Factories\ContractMethodProcessorFactory::create($accountBlock->contractMethod);
    ////        $blockProcessorClass::run($accountBlock);
    ////    });
    //
    //    dd('done');

    $accountBlock = App\Domains\Nom\Models\AccountBlock::firstWhere('hash', 'a7ab32c6f367fa4fc04177fbab35ed5b07e952b3607e4e49750d1e68a8318c4c');
    $blockProcessorClass = App\Domains\Indexer\Factories\ContractMethodProcessorFactory::create($accountBlock->contractMethod);
    $blockProcessorClass::run($accountBlock);

    dd('done');

    //    App\Domains\Nom\Models\AccountBlock::with('data', 'contractMethod', 'contractMethod.contract')
    //        ->whereRelation('contractMethod.contract', 'name', 'Token')
    ////        ->whereHas('contractMethod', function ($query) {
    ////            $query->whereIn('name', ['Delegate', 'Undelegate']);
    ////        })
    //        ->chunk(1000, function (Illuminate\Support\Collection $accountBlocks) {
    //            $accountBlocks->each(function ($accountBlock) {
    //                $blockProcessorClass = App\Domains\Indexer\Factories\ContractMethodProcessorFactory::create($accountBlock->contractMethod);
    //                $blockProcessorClass::run($accountBlock);
    //            });
    //        });

    //    $znn = app(\App\Domains\Nom\Services\ZenonSdk::class);
    //
    //    \App\Domains\Nom\Models\AccountBlockData::with('accountBlock', 'accountBlock.contractMethod', 'accountBlock.contractMethod.contract')
    //        ->chunk(1000, function (\Illuminate\Support\Collection $blockDatas) use ($znn) {
    //            foreach ($blockDatas as $blockData) {
    //
    //                $contractMethod = $blockData->accountBlock->contractMethod;
    //
    //                if (! $contractMethod) {
    //                    continue;
    //                }
    //
    //                $rawData = base64_decode($blockData->raw);
    //                $decoded = $znn->abiDecode($contractMethod, $rawData);
    //
    //                $blockData->decoded = $decoded;
    //                $blockData->save();
    //            }
    //        });
    //
    //    dd('.');

    // Delegate
    //    $accountBlock = \App\Domains\Nom\Models\AccountBlock::find(14309);
    //    \App\Domains\Indexer\Actions\Pillar\Delegate::run($accountBlock);

    // Undelegate
    //    $accountBlock = \App\Domains\Nom\Models\AccountBlock::find(21040);
    //    \App\Domains\Indexer\Actions\Pillar\Undelegate::run($accountBlock);

    // Update pillar
    //    $accountBlock = \App\Domains\Nom\Models\AccountBlock::find(15726);
    //    \App\Domains\Indexer\Actions\Pillar\UpdatePillar::run($accountBlock);

    // Register pillar
    //    $accountBlock = \App\Domains\Nom\Models\AccountBlock::find(16731);
    //    \App\Domains\Indexer\Actions\Pillar\Register::run($accountBlock);

    // Register legacy pillar
    //    $accountBlock = \App\Domains\Nom\Models\AccountBlock::find(17912);
    //    \App\Domains\Indexer\Actions\Pillar\RegisterLegacy::run($accountBlock);

    // Revoke pillar
    //    $accountBlock = App\Domains\Nom\Models\AccountBlock::find(647636);
    //    App\Domains\Indexer\Actions\Pillar\Revoke::run($accountBlock);

    // Create project
    //    $accountBlock = App\Domains\Nom\Models\AccountBlock::find(568484);
    //    App\Domains\Indexer\Actions\Accelerator\CreateProject::run($accountBlock);

    // Add phase
    //    $accountBlock = App\Domains\Nom\Models\AccountBlock::find(605658);
    //    App\Domains\Indexer\Actions\Accelerator\AddPhase::run($accountBlock);

    // Phase updated
    $accountBlock = App\Domains\Nom\Models\AccountBlock::find(611319);
    App\Domains\Indexer\Actions\Accelerator\UpdatePhase::run($accountBlock);

    dd('done');

    $test = $account
        ->delegations()
        ->wherePivotNull('ended_at')
        ->get();

    dd($test->first()->pivot->display_duration, $test->first()->pivot->ended_at);

    $account
        ->delegations()
        ->newPivotStatementForId($account->id)
        ->where('ended_at', null)
        ->update(['ended_at' => now()]);

    $account->delegations()->attach($pillar->id, [
        'started_at' => now(),
    ]);

    dd('done');

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

    // XEGGEX
    //    $apiKey = '8ec015f680d33a11abe518706711060b';
    //    $apiSecret = '2934e33613c49a5d96d2e4c4541aaa11d3300d33c9d1348d';
    //    $baseUrl = 'https://api.xeggex.com/api/v2';
    //
    //    //$ticker = Http::withToken($apiKey.':'.$apiSecret, 'Basic')->get($baseUrl.'/market/getbysymbol/ZNN_USDT')->json();
    //    /**
    //     * Ticker
    //     * ticker_id = symbol
    //     * base_currency = primaryTicker
    //     * target_currency = ?
    //     * last_price = lastPrice
    //     * base_volume = volume
    //     * target_volume = ?
    //     * bid = bestBid
    //     * ask = bestAsk
    //     * high = highPrice
    //     * low = lowPrice
    //     *
    //     *
    //     * "_id" => "664f53aabeff051fd3301807"
    //     * "symbol" => "ZNN/USDT"
    //     * "primaryName" => "Zenon"
    //     * "primaryTicker" => "ZNN"
    //     * "lastPrice" => "0.565791"
    //     * "yesterdayPrice" => "0.633919"
    //     * "highPrice" => "0.797121"
    //     * "lowPrice" => "0.450001"
    //     * "volume" => "463.7462"
    //     * "lastTradeAt" => 1717357553572
    //     * "priceDecimals" => 6
    //     * "quantityDecimals" => 4
    //     * "isActive" => true
    //     * "primaryAsset" => "65d979fe2fb89f13b1fc2006"
    //     * "secondaryAsset" => "613b398ed7a0bd1a304f963e"
    //     * "imageUUID" => "1af8dc72-9b35-4d1b-b566-1ed23cf7e803"
    //     * "engineId" => 5
    //     * "isPaused" => false
    //     * "bestAsk" => "0.570619"
    //     * "bestBid" => "0.56579"
    //     * "createdAt" => 1716474794843
    //     * "updatedAt" => 1717357855114
    //     * "primaryUsdValue" => "0.575736900000"
    //     * "primaryCirculation" => ""
    //     * "secondaryUsdValue" => "1"
    //     * "secondaryCirculation" => "78311766178.46"
    //     * "lastPriceUpDown" => "down"
    //     * "spreadPercent" => "0.846"
    //     * "changePercent" => "-10.74"
    //     * "volumeSecondary" => "285.9444"
    //     * "lastPriceNumber" => 0.565791
    //     * "bestBidNumber" => 0.56579
    //     * "bestAskNumber" => 0.570619
    //     * "yesterdayPriceNumber" => 0.633919
    //     * "changePercentNumber" => -10.74
    //     * "highPriceNumber" => 0.797121
    //     * "lowPriceNumber" => 0.450001
    //     * "volumeNumber" => 463.7462
    //     * "volumeSecondaryNumber" => 285.9444
    //     * "volumeUsdNumber" => 285.94
    //     * "marketcapNumber" => 0
    //     * "lineChart" => "[]"
    //     * "minimumQuantity" => 0
    //     * "maxAllowedPrice" => ""
    //     * "minAllowedPrice" => ""
    //     * "pauseBuys" => false
    //     * "pauseSells" => false
    //     * "assignedWebsites" => "629b1213681675f1a93444f4"
    //     * "spreadPercentNumber" => 0.846
    //     * "id" => "664f53aabeff051fd3301807"
    //     */
    //
    //    //$market = Http::withToken($apiKey.':'.$apiSecret, 'Basic')->get($baseUrl.'/market/getorderbookbysymbol/ZNN_USDT')->json();
    //    /**
    //     * Market
    //     *
    //     * ticker_id = symbol
    //     * timestamp = timestamp
    //     * bids = bids
    //     * asks = asks
    //     */

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
