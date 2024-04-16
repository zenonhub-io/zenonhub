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

uses()->group('nom', 'nom-services', 'indexer');

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(TestDatabaseSeeder::class);

    $this->mock(ZenonSdk::class, function (MockInterface $mock) {

        // setup the mock to return predefined Json for specific calls
        $momentumsJson = Storage::json('nom-json/test/momentums.json');
        $accountBlocksJson = Storage::json('nom-json/test/transactions.json');
        $momentumDTOs = MomentumDTO::collect($momentumsJson, Collection::class);
        $accountBlockDTOs = AccountBlockDTO::collect($accountBlocksJson, Collection::class);

        $mock->shouldReceive('getFrontierMomentum')
            ->andReturn($momentumDTOs->last());

        $mock->shouldReceive('getMomentumsByHeight')
            ->andReturn($momentumDTOs);

        $hashes = [
            'txAddr1000000000000000000000000000000000000000000000000000000001',
            'txAddr1000000000000000000000000000000000000000000000000000000002',
            'txAddr1000000000000000000000000000000000000000000000000000000003',
            'txAddr1000000000000000000000000000000000000000000000000000000004',
            'txAddr2000000000000000000000000000000000000000000000000000000001',
            'embedpyllar00000000000000000000000000000000000000000000000000001',
            'embedpyllar00000000000000000000000000000000000000000000000000002',
            'embedt0ken000000000000000000000000000000000000000000000000000001',
            'embedt0ken000000000000000000000000000000000000000000000000000002',
        ];

        foreach ($hashes as $hash) {
            $mock->shouldReceive('getAccountBlockByHash')
                ->withArgs([$hash])
                ->andReturn($accountBlockDTOs->firstWhere('hash', $hash));
        }
    });
});

it('respects the lock', function () {
    Cache::lock('indexerLock', 0, 'indexer')->get();

    app(Indexer::class)->run();

    expect(Momentum::count())->toBe(0);
});

it('respects the emergency lock', function () {

    Cache::lock('indexerEmergencyLock', 0, 'indexer')->get();

    app(Indexer::class)->run();

    expect(Momentum::count())->toBe(0);
});

it('inserts momentums', function () {

    app(Indexer::class)->run();

    expect(Momentum::count())->toBe(6);
});

it('inserts account blocks', function () {

    app(Indexer::class)->run();

    expect(AccountBlock::count())->toBe(8);
});
