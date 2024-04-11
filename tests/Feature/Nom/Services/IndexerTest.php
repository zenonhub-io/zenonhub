<?php

declare(strict_types=1);

use App\Domains\Nom\DataTransferObjects\AccountBlockDTO;
use App\Domains\Nom\DataTransferObjects\MomentumDTO;
use App\Domains\Nom\Models\Momentum;
use App\Domains\Nom\Services\Indexer;
use App\Domains\Nom\Services\ZenonSdk;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\TestDatabaseSeeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Mockery\MockInterface;

const INDEXER_TEST_TX_HASH1 = 'txAddr1000000000000000000000000000000000000000000000000000000002';
const INDEXER_TEST_TX_HASH2 = 'txAddr2000000000000000000000000000000000000000000000000000000002';

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(TestDatabaseSeeder::class);

    $this->mock(ZenonSdk::class, function (MockInterface $mock) {

        // setup the mock to return predefined Json for specific calls
        $momentumsJson = Storage::json('nom-json/test/momentums.json');
        $momentumDTOs = MomentumDTO::collect($momentumsJson, Collection::class);

        $accountBlocksJson = Storage::json('nom-json/test/transactions.json');
        $accountBlockDTOs = AccountBlockDTO::collect($accountBlocksJson, Collection::class);

        $mock->shouldReceive('getFrontierMomentum')
            ->andReturn($momentumDTOs->last());

        $mock->shouldReceive('getMomentumsByHeight')
            ->andReturn($momentumDTOs);

        $mock->shouldReceive('getAccountBlockByHash')
            ->withArgs([INDEXER_TEST_TX_HASH1])
            ->andReturn($accountBlockDTOs->where('hash', INDEXER_TEST_TX_HASH1)->first());

        $mock->shouldReceive('getAccountBlockByHash')
            ->withArgs([INDEXER_TEST_TX_HASH2])
            ->andReturn($accountBlockDTOs->where('hash', INDEXER_TEST_TX_HASH2)->first());
    });
});

it('respects the lock', function () {
    Cache::lock('indexerLock', 0, 'indexer')->get();

    app(Indexer::class)->run();

    expect(Momentum::count())->toBe(1);
})->group('nom-services', 'indexer');

it('respects the emergency lock', function () {

    Cache::lock('indexerEmergencyLock', 0, 'indexer')->get();

    app(Indexer::class)->run();

    expect(Momentum::count())->toBe(1);

})->group('nom-services', 'indexer');

it('inserts five momentums', function () {

    app(Indexer::class)->run();

    $momentumCount = Momentum::count();
    $latestMomentum = Momentum::latest()->first();

    expect($momentumCount)->toBe(5)
        ->and($latestMomentum->hash)->toBe('momentum00000000000000000000000000000000000000000000000000000005');

})->group('nom-services', 'indexer');
