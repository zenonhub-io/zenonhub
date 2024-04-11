<?php

declare(strict_types=1);

use App\Domains\Nom\Actions\InsertMomentum;
use App\Domains\Nom\DataTransferObjects\MomentumDTO;
use App\Domains\Nom\Events\MomentumInserted;
use App\Domains\Nom\Models\Momentum;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\TestDatabaseSeeder;
use Illuminate\Support\Collection;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(TestDatabaseSeeder::class);

    $momentumsJson = Storage::json('nom-json/test/momentums.json');
    $this->momentumDTO = MomentumDTO::collect($momentumsJson, Collection::class)->first();
});

it('inserts a momentum', function () {

    (new InsertMomentum)->execute($this->momentumDTO);

    expect(Momentum::count())->toBe(1)
        ->and(Momentum::first()->hash)->toEqual($this->momentumDTO->hash);

})->group('nom-actions', 'insert-momentum');

it('assigns the momentum to the correct producer account', function () {

    (new InsertMomentum)->execute($this->momentumDTO);

    expect(Momentum::first()->producerAccount->address)->toBe($this->momentumDTO->producer);

})->group('nom-actions', 'insert-momentum');

it('assigns the momentum to the correct pillar', function () {

    (new InsertMomentum)->execute($this->momentumDTO);

    expect(Momentum::first()->producerPillar->name)->toBe('Pillar1');

})->group('nom-actions', 'insert-momentum');

it('inserts dispatches the momentum inserted event', function () {

    Event::fake();

    (new InsertMomentum)->execute($this->momentumDTO);

    Event::assertDispatched(MomentumInserted::class);

})->group('nom-actions', 'insert-momentum');
