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

    $accountBlock = App\Domains\Nom\Models\AccountBlock::findBy('hash', 'da21752ee638d10f2bb2205935d34b62af94618b92c468aeb784cb704bf0b35f');

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
