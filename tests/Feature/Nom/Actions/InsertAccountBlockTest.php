<?php

declare(strict_types=1);

use App\Domains\Indexer\Actions\InsertAccountBlock;
use App\Domains\Indexer\Events\AccountBlockInserted;
use App\Domains\Nom\DataTransferObjects\AccountBlockDTO;
use App\Domains\Nom\DataTransferObjects\MomentumDTO;
use App\Domains\Nom\Models\Account;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\ContractMethod;
use App\Domains\Nom\Models\Momentum;
use App\Domains\Nom\Services\ZenonSdk;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\TestDatabaseSeeder;
use Illuminate\Support\Collection;
use Mockery\MockInterface;

uses()->group('nom', 'nom-actions', 'insert-account-block');

beforeEach(function () {

    $this->seed(DatabaseSeeder::class);
    $this->seed(TestDatabaseSeeder::class);

    $accountBlocksJson = Storage::json('nom-json/test/transactions.json');
    $momentumsJson = Storage::json('nom-json/test/momentums.json');
    $this->accountBlockDTOs = AccountBlockDTO::collect($accountBlocksJson, Collection::class);
    $this->momentumDTOs = MomentumDTO::collect($momentumsJson, Collection::class);

    $this->mock(ZenonSdk::class, function (MockInterface $mock) {

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

    $this->momentumDTOs->each(function ($momentumDTO) {
        Momentum::insert([
            'chain_id' => 1,
            'producer_account_id' => 1,
            'producer_pillar_id' => 1,
            'version' => $momentumDTO->version,
            'height' => $momentumDTO->height,
            'hash' => $momentumDTO->hash,
            'data' => $momentumDTO->data,
            'created_at' => $momentumDTO->timestamp,
        ]);
    });
});

it('inserts a account block', function () {

    $momentumContent = $this->momentumDTOs->firstWhere('height', 2)->content->first();

    app(InsertAccountBlock::class)->execute($momentumContent);

    expect(AccountBlock::count())->toBe(1)
        ->and(AccountBlock::find(1)->hash)->toEqual('txAddr1000000000000000000000000000000000000000000000000000000001');

});

it('relates an account block to a momentum', function () {

    $momentumDTO = $this->momentumDTOs->firstWhere('height', 2);
    app(InsertAccountBlock::class)->execute($momentumDTO->content->first());

    $accountBlock = AccountBlock::find(1);
    $momentum = Momentum::firstWhere('hash', $momentumDTO->hash);

    expect($accountBlock->momentum->hash)->toEqual($momentum->hash)
        ->and($accountBlock->momentum->height)->toEqual(2)
        ->and($momentum->accountBlocks->count())->toBe(1);

});

it('sets an accounts public key', function () {

    $momentumDTO = $this->momentumDTOs->firstWhere('height', 2);
    app(InsertAccountBlock::class)->execute($momentumDTO->content->first());

    $account = Account::findBy('address', 'z1qxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxaddr1');
    $accountBlockDTO = $this->accountBlockDTOs->firstWhere('hash', 'txAddr1000000000000000000000000000000000000000000000000000000001');

    expect($account->public_key)->toEqual($accountBlockDTO->publicKey);
});

it('sets an accounts first active date', function () {

    $momentumDTO = $this->momentumDTOs->firstWhere('height', 2);
    app(InsertAccountBlock::class)->execute($momentumDTO->content->first());

    $account = Account::findBy('address', 'z1qxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxaddr1');

    expect($account->first_active_at->timestamp)->toEqual($momentumDTO->timestamp);
});

it('relates an account block to the correct account', function () {

    $momentumDTOs = $this->momentumDTOs->whereBetween('height', [2, 3]);
    $momentumDTOs->each(function ($momentumDTO) {
        $momentumDTO->content->each(function ($momentumContentDTO) {
            app(InsertAccountBlock::class)->execute($momentumContentDTO);
        });
    });

    $accountOne = Account::findBy('address', 'z1qxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxaddr1');
    $accountTwo = Account::findBy('address', 'z1qxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxaddr2');
    $accountBlock = AccountBlock::find(1);

    expect($accountOne->sentBlocks->count())->toEqual(2)
        ->and($accountTwo->receivedBlocks->count())->toEqual(2)
        ->and($accountTwo->sentBlocks->count())->toEqual(1)
        ->and($accountBlock->account->address)->toEqual($accountOne->address)
        ->and($accountBlock->toAccount->address)->toEqual($accountTwo->address);

});

it('associates account block with paired block', function () {

    $momentumDTOs = $this->momentumDTOs->whereBetween('height', [2, 3]);
    $momentumDTOs->each(function ($momentumDTO) {
        $momentumDTO->content->each(function ($momentumContentDTO) {
            app(InsertAccountBlock::class)->execute($momentumContentDTO);
        });
    });

    $firstAccountBlock = AccountBlock::findBy('hash', 'txAddr1000000000000000000000000000000000000000000000000000000001', true);
    $secondAccountBlock = AccountBlock::findBy('hash', 'txAddr2000000000000000000000000000000000000000000000000000000001', true);
    $unpairedAccountBlock = AccountBlock::findBy('hash', 'txAddr1000000000000000000000000000000000000000000000000000000002', true);

    expect($firstAccountBlock->pairedAccountBlock->hash)->toEqual($secondAccountBlock->hash)
        ->and($secondAccountBlock->pairedAccountBlock->hash)->toEqual($firstAccountBlock->hash)
        ->and($unpairedAccountBlock->pairedAccountBlock)->toBeNull();

});

it('associates parent and descendant blocks', function () {

    $momentumDTOs = $this->momentumDTOs->whereBetween('height', [4, 5, 6]);
    $momentumDTOs->each(function ($momentumDTO) {
        $momentumDTO->content->each(function ($momentumContentDTO) {
            app(InsertAccountBlock::class)->execute($momentumContentDTO);
        });
    });

    $parentAccountBlock = AccountBlock::findBy('hash', 'embedpyllar00000000000000000000000000000000000000000000000000002', true);
    $childAccountBlock = AccountBlock::findBy('hash', 'embedpyllar00000000000000000000000000000000000000000000000000001', true);

    expect($parentAccountBlock->descendants->count())->toEqual(1)
        ->and($parentAccountBlock->descendants->first()->hash)->toEqual($childAccountBlock->hash)
        ->and($childAccountBlock->parent)->not->toBeNull()
        ->and($childAccountBlock->parent->hash)->toEqual($parentAccountBlock->hash);
});

it('associates to a contract and contract method', function () {

    $blockHash = 'embedpyllar00000000000000000000000000000000000000000000000000001';
    $decodedData = json_decode('{"tokenStandard":"zts1znnxxxxxxxxxxxxx9z4ulx","amount":"315424208","receiveAddress":"z1qp43vnn4qvlxkwenvceukvau5m33n6we4rt3aj"}', true);

    $momentumContent = $this->momentumDTOs->firstWhere('height', 5)->content->firstWhere('hash', $blockHash);
    app(InsertAccountBlock::class)->execute($momentumContent);

    $accountBlock = AccountBlock::findBy('hash', $blockHash, true);

    expect($accountBlock->data)->not->toBeNull()
        ->and($accountBlock->contractMethod->contract->name)->toEqual('Token')
        ->and($accountBlock->contractMethod->name)->toEqual('Mint')
        ->and($accountBlock->data->decoded)->toEqual($decodedData);
});

it('associates raw and decoded data', function () {

    $blockHash = 'embedpyllar00000000000000000000000000000000000000000000000000001';
    $rawData = 'zXD5vAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAU5mMYxjGMYxjGAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABLM/dAAAAAAAAAAAAAAAAAAaxZOdQM+azszZjPLM7ym4xnp2Q==';
    $decodedData = json_decode('{"tokenStandard":"zts1znnxxxxxxxxxxxxx9z4ulx","amount":"315424208","receiveAddress":"z1qp43vnn4qvlxkwenvceukvau5m33n6we4rt3aj"}', true);

    $momentumContent = $this->momentumDTOs->firstWhere('height', 5)->content->firstWhere('hash', $blockHash);
    app(InsertAccountBlock::class)->execute($momentumContent);

    $accountBlock = AccountBlock::findBy('hash', $blockHash, true);

    expect($accountBlock->data)->not->toBeNull()
        ->and($accountBlock->data->raw)->toEqual($rawData)
        ->and($accountBlock->data->decoded)->toEqual($decodedData);
});

it('dispatches account block inserted event once for each account block', function () {

    Event::fake();

    $momentumContent = $this->momentumDTOs->firstWhere('height', 2)->content->first();
    app(InsertAccountBlock::class)->execute($momentumContent);

    Event::assertDispatchedTimes(AccountBlockInserted::class, 1);

});