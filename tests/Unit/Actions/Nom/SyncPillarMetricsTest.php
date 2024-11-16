<?php

declare(strict_types=1);

use App\Actions\Sync\PillarMetrics;
use App\DataTransferObjects\Nom\PillarDTO;
use App\Models\Nom\Pillar;
use App\Services\ZenonSdk\ZenonSdk;
use Database\Seeders\Nom\Test\PillarsSeeder;
use Database\Seeders\NomSeeder;
use Illuminate\Support\Collection;
use Mockery\MockInterface;

uses()->group('nom', 'nom-actions', 'sync-pillar-metrics');

beforeEach(function () {

    $this->seed(NomSeeder::class);
    $this->seed(PillarsSeeder::class);

    $pillarJson = Storage::json('nom-json/test/pillars.json');
    $this->pillarDTOs = PillarDTO::collect($pillarJson, Collection::class);

    $this->mock(ZenonSdk::class, function (MockInterface $mock) {

        $pillarDTO = $this->pillarDTOs->firstWhere('name', 'Pillar1');
        $callCount = 0;

        $mock->shouldReceive('getPillarByName')
            ->withArgs(['Pillar1'])
            ->andReturnUsing(function () use (&$callCount, $pillarDTO) {

                $callCount++;

                switch ($callCount) {
                    case 1:
                        $pillarDTO->weight = '5';
                        $pillarDTO->rank = 5;
                        $pillarDTO->currentStats->producedMomentums = 5;
                        $pillarDTO->currentStats->expectedMomentums = 5;
                        break;
                    case 2:
                        $pillarDTO->currentStats->producedMomentums = 5;
                        $pillarDTO->currentStats->expectedMomentums = 10;
                        break;
                    case 3:
                        $pillarDTO->currentStats->producedMomentums = 5;
                        $pillarDTO->currentStats->expectedMomentums = 15;
                        break;
                    case 4:
                        $pillarDTO->currentStats->producedMomentums = 20;
                        $pillarDTO->currentStats->expectedMomentums = 20;
                        break;
                }

                return $pillarDTO;
            });
    });
});

it('syncs pillar data', function () {

    $pillar = Pillar::firstWhere('name', 'Pillar1');

    (new PillarMetrics)->handle($pillar);

    $pillar = $pillar->fresh();

    expect($pillar->expected_momentums)->toBe(5)
        ->and($pillar->produced_momentums)->toEqual(5)
        ->and($pillar->rank)->toEqual(5)
        ->and($pillar->weight)->toEqual('5')
        ->and($pillar->missed_momentums)->toEqual(0);
});

it('counts missed momentums', function () {

    $pillar = Pillar::firstWhere('name', 'Pillar1');

    (new PillarMetrics)->handle($pillar);
    (new PillarMetrics)->handle($pillar);
    (new PillarMetrics)->handle($pillar);

    $pillar = $pillar->fresh();

    expect($pillar->expected_momentums)->toBe(15)
        ->and($pillar->produced_momentums)->toEqual(5)
        ->and($pillar->missed_momentums)->toEqual(1);
});

it('resets the missed momentum count', function () {

    $pillar = Pillar::firstWhere('name', 'Pillar1');

    (new PillarMetrics)->handle($pillar);
    (new PillarMetrics)->handle($pillar);
    (new PillarMetrics)->handle($pillar);
    (new PillarMetrics)->handle($pillar);

    $pillar = $pillar->fresh();

    expect($pillar->expected_momentums)->toBe(20)
        ->and($pillar->produced_momentums)->toEqual(20)
        ->and($pillar->missed_momentums)->toEqual(0);
});
