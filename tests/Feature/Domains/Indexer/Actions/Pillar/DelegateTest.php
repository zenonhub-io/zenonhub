<?php

declare(strict_types=1);

use App\Domains\Indexer\Actions\Pillar\Delegate;
use App\Domains\Indexer\DataTransferObjects\MockAccountBlockDTO;
use App\Domains\Indexer\Events\Pillar\AccountDelegated;
use App\Domains\Indexer\Factories\MockAccountBlockFactory;
use App\Domains\Nom\Enums\AccountBlockTypesEnum;
use App\Domains\Nom\Enums\EmbeddedContractsEnum;
use App\Domains\Nom\Enums\NetworkTokensEnum;
use App\Domains\Nom\Models\Account;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\ContractMethod;
use App\Domains\Nom\Models\Pillar;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\NomSeeder;
use Database\Seeders\TestGenesisSeeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

uses()->group('indexer', 'indexer-actions', 'pillar-delegate');

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(NomSeeder::class);
    $this->seed(TestGenesisSeeder::class);
});

function createDelegateAccountBlock(array $overrides = []): AccountBlock
{
    $account = $overrides['account'] ?? Account::factory()->create();

    $default = [
        'account' => $account,
        'toAccount' => load_account(EmbeddedContractsEnum::PILLAR->value),
        'token' => load_token(NetworkTokensEnum::ZNN->value),
        'amount' => (string) 0,
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Pillar', 'Delegate'),
        'data' => '{"name":"Test"}',
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('delegates to a pillar', function () {

    Pillar::factory()->create([
        'name' => 'Test',
    ]);
    $accountBlock = createDelegateAccountBlock();

    (new Delegate)->handle($accountBlock);

    $pillar = Pillar::firstWhere('name', 'Test');
    $account = $accountBlock->account;

    expect($pillar->delegators()->get())->toHaveCount(1)
        ->and($pillar->delegators()->first()->id)->toEqual($account->id)
        ->and($pillar->delegators()->first()->pivot->started_at)->toEqual($accountBlock->created_at)
        ->and($account->delegations()->get())->toHaveCount(1)
        ->and($account->delegations()->first()->id)->toEqual($pillar->id);
});

it('dispatches the account delegated event', function () {

    Pillar::factory()->create([
        'name' => 'Test',
    ]);
    $accountBlock = createDelegateAccountBlock();

    Event::fake();

    (new Delegate)->handle($accountBlock);

    Event::assertDispatched(AccountDelegated::class);
});

it('ensure only valid pillars can be delegated to', function () {

    Pillar::factory()->create([
        'name' => 'Test1',
    ]);
    $accountBlock = createDelegateAccountBlock();
    $account = $accountBlock->account;

    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Pillar: Delegate failed',
            Mockery::on(fn ($data) => $data['error'] === 'Invalid pillar')
        )
        ->once();

    (new Delegate)->handle($accountBlock);

    expect($account->delegations()->get())->toHaveCount(0);
});

it('ensure only active pillars can be delegated to', function () {

    Pillar::factory()->revoked()->create([
        'name' => 'Test',
    ]);
    $accountBlock = createDelegateAccountBlock();
    $account = $accountBlock->account;

    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Pillar: Delegate failed',
            Mockery::on(fn ($data) => $data['error'] === 'Pillar is revoked')
        )
        ->once();

    (new Delegate)->handle($accountBlock);

    expect($account->delegations()->get())->toHaveCount(0);
});
