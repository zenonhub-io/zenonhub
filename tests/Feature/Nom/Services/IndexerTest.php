<?php

declare(strict_types=1);

use App\Domains\Nom\DataTransferObjects\AccountBlockDTO;
use App\Domains\Nom\DataTransferObjects\MomentumDTO;
use App\Domains\Nom\Exceptions\ZenonRpcException;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\ContractMethod;
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

    // setup the mock to return predefined Json for specific calls
    $momentumsJson = Storage::json('nom-json/test/momentums.json');
    $accountBlocksJson = Storage::json('nom-json/test/transactions.json');
    $this->momentumDTOs = MomentumDTO::collect($momentumsJson, Collection::class);
    $this->accountBlockDTOs = AccountBlockDTO::collect($accountBlocksJson, Collection::class);

    $this->mock(ZenonSdk::class, function (MockInterface $mock) {

        $mock->shouldReceive('getFrontierMomentum')
            ->andReturn($this->momentumDTOs->last());

        $mock->shouldReceive('getMomentumsByHeight')
            ->andReturn($this->momentumDTOs);

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
                ->andReturn($this->accountBlockDTOs->firstWhere('hash', $hash));
        }

        $apiDataResponses = [
            'r0PT8A==' => null,
            'zXD5vAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAU5mMYxjGMYxjGAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABLM/dAAAAAAAAAAAAAAAAAAaxZOdQM+azszZjPLM7ym4xnp2Q==' => '{"tokenStandard":"zts1znnxxxxxxxxxxxxx9z4ulx","amount":"315424208","receiveAddress":"z1qp43vnn4qvlxkwenvceukvau5m33n6we4rt3aj"}',
        ];

        foreach ($apiDataResponses as $input => $output) {

            $input = base64_decode($input);
            $output = $output ? json_decode($output, true) : null;

            $mock->shouldReceive('abiDecode')
                ->withArgs([ContractMethod::class, $input])
                ->andReturn($output);
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

it('rolls back on exception', function () {

    $this->mock(ZenonSdk::class, function (MockInterface $mock) {
        $mock->shouldReceive('getFrontierMomentum')
            ->andReturn($this->momentumDTOs->last());

        $mock->shouldReceive('getMomentumsByHeight')
            ->andReturn($this->momentumDTOs);

        $hashes = [
            'txAddr1000000000000000000000000000000000000000000000000000000001',
            'txAddr1000000000000000000000000000000000000000000000000000000002',
            'txAddr1000000000000000000000000000000000000000000000000000000003',
            'txAddr2000000000000000000000000000000000000000000000000000000001',
        ];

        foreach ($hashes as $hash) {
            if ($hash === 'txAddr2000000000000000000000000000000000000000000000000000000001') {
                $mock->shouldReceive('getAccountBlockByHash')
                    ->withArgs([$hash])
                    ->andThrow(new ZenonRpcException('Unable to load data'));
            } else {
                $mock->shouldReceive('getAccountBlockByHash')
                    ->withArgs([$hash])
                    ->andReturn($this->accountBlockDTOs->firstWhere('hash', $hash));
            }
        }
    });

    app(Indexer::class)->run();

    expect(Momentum::count())->toBe(2)
        ->and(AccountBlock::count())->toBe(2);
});
