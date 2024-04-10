<?php

declare(strict_types=1);

use App\Domains\Nom\Actions\InsertAccountBlock;
use App\Domains\Nom\Actions\InsertMomentum;
use App\Domains\Nom\DataTransferObjects\AccountBlockDTO;
use App\Domains\Nom\DataTransferObjects\MomentumDTO;
use App\Domains\Nom\Events\AccountBlockInserted;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Services\ZenonSdk;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\TestGenesisSeeder;
use Illuminate\Support\Collection;
use Mockery\MockInterface;

const HASH1 = '1000000000000000000000000000000000000000000000000000000000000002';
const HASH2 = '2000000000000000000000000000000000000000000000000000000000000002';

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(TestGenesisSeeder::class);
    $this->mock(ZenonSdk::class, function (MockInterface $mock) {
        $accountBlocksJson = Storage::json('nom-json/test/transactions.json');
        $accountBlockDTOs = AccountBlockDTO::collect($accountBlocksJson, Collection::class);

        $mock->shouldReceive('getAccountBlockByHash')
            ->withArgs([HASH1])
            ->andReturn($accountBlockDTOs->where('hash', HASH1)->first());

        $mock->shouldReceive('getAccountBlockByHash')
            ->withArgs([HASH2])
            ->andReturn($accountBlockDTOs->where('hash', HASH2)->first());
    });
});

it('inserts a account block', function () {

    $momentumsJson = Storage::json('nom-json/test/momentums.json');
    MomentumDTO::collect($momentumsJson, Collection::class)
        ->each(function (MomentumDTO $momentumDTO) {
            (new InsertMomentum)->execute($momentumDTO);
            $momentumDTO->content->each(function ($momentumContentDTO) {
                (new InsertAccountBlock)->execute($momentumContentDTO);
            });
        });

    $totalAccountBlocks = AccountBlock::count();
    $firstAccountBlock = AccountBlock::findBy('hash', HASH1, true);
    $secondAccountBlock = AccountBlock::findBy('hash', HASH2, true);

    expect($totalAccountBlocks)->toBe(8)
        ->and($firstAccountBlock)->not->toBeEmpty()
        ->and($secondAccountBlock)->not->toBeEmpty();

})->group('nom-actions', 'insert-account-block');

it('relates an account block to a momentum', function () {

    $momentumsJson = Storage::json('nom-json/test/momentums.json');
    MomentumDTO::collect($momentumsJson, Collection::class)
        ->each(function (MomentumDTO $momentumDTO) {
            (new InsertMomentum)->execute($momentumDTO);
            $momentumDTO->content->each(function ($momentumContentDTO) {
                (new InsertAccountBlock)->execute($momentumContentDTO);
            });
        });

    $firstAccountBlock = AccountBlock::findBy('hash', HASH1, true);

    expect($firstAccountBlock->momentum->hash)->toEqual('0000000000000000000000000000000000000000000000000000000000000002')
        ->and($firstAccountBlock->momentum->height)->toEqual(2);

})->group('nom-actions', 'insert-account-block');

it('associates account block with paired block', function () {

    $momentumsJson = Storage::json('nom-json/test/momentums.json');
    $momentumDTOs = MomentumDTO::collect($momentumsJson, Collection::class);

    $momentumDTOs->each(function (MomentumDTO $momentumDTO) {
        (new InsertMomentum)->execute($momentumDTO);
        $momentumDTO->content->each(function ($momentumContentDTO) {
            (new InsertAccountBlock)->execute($momentumContentDTO);
        });
    });

    $firstAccountBlock = AccountBlock::findBy('hash', HASH1, true);
    $secondAccountBlock = AccountBlock::findBy('hash', HASH2, true);

    expect($firstAccountBlock->pairedAccountBlock->hash)->toEqual(HASH2)
        ->and($secondAccountBlock->pairedAccountBlock->hash)->toEqual(HASH1);

})->group('nom-actions', 'insert-account-block');

it('dispatches account block inserted event twice when two account blocks are inserted', function () {

    Event::fake();

    $momentumsJson = Storage::json('nom-json/test/momentums.json');
    $momentumDTOs = MomentumDTO::collect($momentumsJson, Collection::class);

    $momentumDTOs->each(function (MomentumDTO $momentumDTO) {
        (new InsertMomentum)->execute($momentumDTO);
        $momentumDTO->content->each(function ($momentumContentDTO) {
            (new InsertAccountBlock)->execute($momentumContentDTO);
        });
    });

    Event::assertDispatchedTimes(AccountBlockInserted::class, 2);

})->group('nom-actions', 'insert-account-block');
