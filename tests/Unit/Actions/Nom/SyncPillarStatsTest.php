<?php

declare(strict_types=1);

use App\Actions\Nom\SyncPillarStats;
use App\Models\Nom\Pillar;
use App\Models\Nom\PillarStatHistory;
use Database\Seeders\Nom\Test\PillarsSeeder;
use Database\Seeders\NomSeeder;

uses()->group('nom', 'nom-actions', 'sync-pillar-stats');

beforeEach(function () {
    $this->seed(NomSeeder::class);
    $this->seed(PillarsSeeder::class);
});

it('creates pillar stat history', function () {

    $pillar = Pillar::firstWhere('name', 'Pillar1');

    (new SyncPillarStats)->handle($pillar);

    $stat = $pillar->statHistory()->first();

    expect(PillarStatHistory::count())->toBe(1)
        ->and($pillar->statHistory()->count())->toEqual(1)
        ->and($stat->rank)->toEqual($pillar->rank)
        ->and($stat->weight)->toEqual($pillar->weight)
        ->and($stat->momentum_rewards)->toEqual($pillar->momentum_rewards)
        ->and($stat->delegate_rewards)->toEqual($pillar->delegate_rewards);
});
