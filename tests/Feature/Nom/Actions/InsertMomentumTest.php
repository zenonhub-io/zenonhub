<?php

declare(strict_types=1);

use App\Domains\Nom\Actions\InsertMomentum;
use App\Domains\Nom\DataTransferObjects\MomentumDTO;
use App\Domains\Nom\Events\MomentumInserted;
use App\Domains\Nom\Models\Momentum;
use App\Domains\Nom\Models\Pillar;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\TestGenesisSeeder;
use Illuminate\Support\Collection;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(TestGenesisSeeder::class);

    $momentumsJson = Storage::json('nom-json/test/momentums.json');
    $this->momentumDTO = MomentumDTO::collect($momentumsJson, Collection::class)->first();
});

it('inserts a momentum', function () {

    (new InsertMomentum)->execute($this->momentumDTO);

    $totalMomentums = Momentum::count();
    $latestMomentum = Momentum::latest()->first();

    expect($totalMomentums)->toBe(2)
        ->and($latestMomentum->hash)->toEqual($this->momentumDTO->hash);

})->group('nom-actions', 'insert-momentum');

it('assigns the momentum to the correct pillar and producer account', function () {

    (new InsertMomentum)->execute($this->momentumDTO);

    $momentum = Momentum::findBy('hash', $this->momentumDTO->hash, true);
    $pillar = Pillar::whereRelation('producerAccount', 'address', $this->momentumDTO->producer)->first();

    expect($momentum->producerPillar->id)->toBe($pillar->id)
        ->and($momentum->producerAccount->address)->toBe($pillar->producerAccount->address);

})->group('nom-actions', 'insert-momentum');

it('inserts dispatches the momentum inserted event', function () {

    Event::fake();

    (new InsertMomentum)->execute($this->momentumDTO);

    Event::assertDispatched(MomentumInserted::class);

})->group('nom-actions', 'insert-momentum');
