<?php

declare(strict_types=1);

use App\Domains\Nom\DataTransferObjects\AccountBlockDTO;
use App\Domains\Nom\DataTransferObjects\MomentumDTO;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Momentum;
use App\Domains\Nom\Services\Indexer;
use App\Domains\Nom\Services\ZenonSdk;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\TestDatabaseSeeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Mockery\MockInterface;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(TestDatabaseSeeder::class);

    $this->hash1 = 'txAddr1000000000000000000000000000000000000000000000000000000001';
    $this->hash2 = 'txAddr1000000000000000000000000000000000000000000000000000000002';
    $this->hash3 = 'txAddr2000000000000000000000000000000000000000000000000000000001';

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
            ->withArgs([$this->hash1])
            ->andReturn($accountBlockDTOs->firstWhere('hash', $this->hash1));

        $mock->shouldReceive('getAccountBlockByHash')
            ->withArgs([$this->hash2])
            ->andReturn($accountBlockDTOs->firstWhere('hash', $this->hash2));

        $mock->shouldReceive('getAccountBlockByHash')
            ->withArgs([$this->hash3])
            ->andReturn($accountBlockDTOs->firstWhere('hash', $this->hash3));
    });
});

it('respects the lock', function () {
    Cache::lock('indexerLock', 0, 'indexer')->get();

    app(Indexer::class)->run();

    expect(Momentum::count())->toBe(0);
})->group('nom-services', 'indexer');

it('respects the emergency lock', function () {

    Cache::lock('indexerEmergencyLock', 0, 'indexer')->get();

    app(Indexer::class)->run();

    expect(Momentum::count())->toBe(0);

})->group('nom-services', 'indexer');

it('inserts momentums', function () {

    app(Indexer::class)->run();

    expect(Momentum::count())->toBe(3);

})->group('nom-services', 'indexer');

it('inserts account blocks', function () {

    app(Indexer::class)->run();

    expect(AccountBlock::count())->toBe(3);

})->group('nom-services', 'indexer');
