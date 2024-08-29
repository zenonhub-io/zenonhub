<?php

declare(strict_types=1);

use App\Actions\Indexer\InsertMomentum;
use App\DataTransferObjects\Nom\MomentumDTO;
use App\Events\Indexer\MomentumInserted;
use App\Models\Nom\Momentum;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\Nom\Test\PillarsSeeder;
use Database\Seeders\NomSeeder;
use Illuminate\Support\Collection;

uses()->group('indexer', 'indexer-actions', 'insert-momentum');

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(NomSeeder::class);
    $this->seed(PillarsSeeder::class);

    $momentumsJson = Storage::json('nom-json/test/momentums.json');
    $this->momentumDTOs = MomentumDTO::collect($momentumsJson, Collection::class);
});

it('inserts a momentum', function () {

    (new InsertMomentum)->execute($this->momentumDTOs->first());

    expect(Momentum::count())->toBe(1)
        ->and(Momentum::first()->hash)->toEqual($this->momentumDTOs->first()->hash);
});

it('assigns the momentum to the correct producer account', function () {

    (new InsertMomentum)->execute($this->momentumDTOs->first());

    expect(Momentum::first()->producerAccount->address)->toBe($this->momentumDTOs->first()->producer);
});

it('assigns the momentum to the correct pillar', function () {

    (new InsertMomentum)->execute($this->momentumDTOs->first());

    expect(Momentum::first()->producerPillar->name)->toBe('Pillar1');
});

it('assigns the momentum to the correct pillar by historic producer address', function () {

    (new InsertMomentum)->execute($this->momentumDTOs->firstWhere('height', 2));

    expect(Momentum::first()->producerPillar->name)->toBe('Pillar2');
});

it('inserts dispatches the momentum inserted event', function () {

    Event::fake();

    (new InsertMomentum)->execute($this->momentumDTOs->first());

    Event::assertDispatched(MomentumInserted::class);
});
