<?php

declare(strict_types=1);

use App\Domains\Nom\Actions\InsertAccountBlock;
use App\Domains\Nom\DataTransferObjects\AccountBlockDTO;
use App\Domains\Nom\DataTransferObjects\MomentumDTO;
use App\Domains\Nom\Events\AccountBlockInserted;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Momentum;
use App\Domains\Nom\Services\ZenonSdk;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\TestDatabaseSeeder;
use Illuminate\Support\Collection;
use Mockery\MockInterface;

beforeEach(function () {

    $this->seed(DatabaseSeeder::class);
    $this->seed(TestDatabaseSeeder::class);

    $this->hash1 = 'txAddr1000000000000000000000000000000000000000000000000000000001';
    $this->hash2 = 'txAddr1000000000000000000000000000000000000000000000000000000002';
    $this->hash3 = 'txAddr2000000000000000000000000000000000000000000000000000000001';

    $accountBlocksJson = Storage::json('nom-json/test/transactions.json');
    $this->accountBlockDTOs = AccountBlockDTO::collect($accountBlocksJson, Collection::class);

    $momentumsJson = Storage::json('nom-json/test/momentums.json');
    $this->momentumDTOs = MomentumDTO::collect($momentumsJson, Collection::class);

    $this->mock(ZenonSdk::class, function (MockInterface $mock) {

        $mock->shouldReceive('getAccountBlockByHash')
            ->withArgs([$this->hash1])
            ->andReturn($this->accountBlockDTOs->where('hash', $this->hash1)->first());

        $mock->shouldReceive('getAccountBlockByHash')
            ->withArgs([$this->hash2])
            ->andReturn($this->accountBlockDTOs->where('hash', $this->hash2)->first());

        $mock->shouldReceive('getAccountBlockByHash')
            ->withArgs([$this->hash3])
            ->andReturn($this->accountBlockDTOs->where('hash', $this->hash3)->first());
    });

    $genesisMomentum = $this->momentumDTOs->where('height', 1)->first();
    $firstMomentum = $this->momentumDTOs->where('height', 2)->first();
    $secondMomentum = $this->momentumDTOs->where('height', 3)->first();

    Momentum::insert([
        [
            'chain_id' => 1,
            'producer_account_id' => 1,
            'producer_pillar_id' => 1,
            'version' => $genesisMomentum->version,
            'height' => $genesisMomentum->height,
            'hash' => $genesisMomentum->hash,
            'data' => $genesisMomentum->data,
            'created_at' => $genesisMomentum->timestamp,
        ], [
            'chain_id' => 1,
            'producer_account_id' => 1,
            'producer_pillar_id' => 1,
            'version' => $firstMomentum->version,
            'height' => $firstMomentum->height,
            'hash' => $firstMomentum->hash,
            'data' => $firstMomentum->data,
            'created_at' => $firstMomentum->timestamp,
        ], [
            'chain_id' => 1,
            'producer_account_id' => 1,
            'producer_pillar_id' => 1,
            'version' => $secondMomentum->version,
            'height' => $secondMomentum->height,
            'hash' => $secondMomentum->hash,
            'data' => $secondMomentum->data,
            'created_at' => $secondMomentum->timestamp,
        ],
    ]);
});

it('inserts a account block', function () {

    $momentumContent = $this->momentumDTOs->firstWhere('height', 2)->content->first();
    (new InsertAccountBlock)->execute($momentumContent);

    expect(AccountBlock::count())->toBe(1)
        ->and(AccountBlock::find(1)->hash)->toEqual($this->hash1);

})->group('nom-actions', 'insert-account-block');

it('relates an account block to a momentum', function () {

    $momentumContent = $this->momentumDTOs->firstWhere('height', 2)->content->first();
    (new InsertAccountBlock)->execute($momentumContent);

    $momentumHash = $this->momentumDTOs->firstWhere('height', 2)->hash;
    $accountBlock = AccountBlock::find(1);
    $momentum = Momentum::firstWhere('hash', $momentumHash);

    expect($accountBlock->momentum->hash)->toEqual($momentumHash)
        ->and($accountBlock->momentum->height)->toEqual(2)
        ->and($momentum->accountBlocks->count())->toBe(1);

})->group('nom-actions', 'insert-account-block');

it('associates account block with paired block', function () {

    $momentumDTOs = $this->momentumDTOs->whereBetween('height', [2, 3]);
    $momentumDTOs->each(function ($momentumDTO) {
        $momentumDTO->content->each(function ($momentumContentDTO) {
            (new InsertAccountBlock)->execute($momentumContentDTO);
        });
    });

    $firstAccountBlock = AccountBlock::findBy('hash', $this->hash1, true);
    $secondAccountBlock = AccountBlock::findBy('hash', $this->hash3, true);
    $unpairedAccountBlock = AccountBlock::findBy('hash', $this->hash2, true);

    expect($firstAccountBlock->pairedAccountBlock->hash)->toEqual($secondAccountBlock->hash)
        ->and($secondAccountBlock->pairedAccountBlock->hash)->toEqual($firstAccountBlock->hash)
        ->and($unpairedAccountBlock->pairedAccountBlock)->toBeNull();

})->group('nom-actions', 'insert-account-block');

it('dispatches account block inserted event once for each account block', function () {

    Event::fake();

    $momentumContent = $this->momentumDTOs->firstWhere('height', 2)->content->first();
    (new InsertAccountBlock)->execute($momentumContent);

    Event::assertDispatchedTimes(AccountBlockInserted::class, 1);

})->group('nom-actions', 'insert-account-block');
