<?php

declare(strict_types=1);

use App\Actions\Indexer\Pillar\Delegate;
use App\DataTransferObjects\MockAccountBlockDTO;
use App\Enums\Nom\AccountBlockTypesEnum;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Enums\Nom\NetworkTokensEnum;
use App\Events\Indexer\Pillar\AccountDelegated;
use App\Factories\MockAccountBlockFactory;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\ContractMethod;
use App\Models\Nom\Pillar;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\Nom\NetworkSeeder;
use Database\Seeders\TestGenesisSeeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

uses()->group('indexer', 'indexer-actions', 'pillar-actions');

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(NetworkSeeder::class);
    $this->seed(TestGenesisSeeder::class);
});

function createDelegateAccountBlock(array $overrides = []): AccountBlock
{
    $account = $overrides['account'] ?? Account::factory()->create();

    $default = [
        'account' => $account,
        'toAccount' => load_account(EmbeddedContractsEnum::PILLAR->value),
        'token' => load_token(NetworkTokensEnum::ZNN->zts()),
        'amount' => (string) 0,
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Pillar', 'Delegate'),
        'data' => [
            'name' => 'Test',
        ],
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

    Delegate::run($accountBlock);

    $pillar = Pillar::firstWhere('name', 'Test');
    $account = $accountBlock->account;

    expect($pillar->delegators()->get())->toHaveCount(1)
        ->and($pillar->delegators()->first()->id)->toEqual($account->id)
        ->and($pillar->delegators()->first()->pivot->started_at)->toEqual($accountBlock->created_at)
        ->and($account->delegations()->get())->toHaveCount(1)
        ->and($account->delegations()->first()->id)->toEqual($pillar->id);
});

it('clears any existing delegations', function () {

    Pillar::factory()->create([
        'name' => 'Test',
    ]);

    $account = Account::factory()->create();
    $account->delegations()->attach(Pillar::factory()->create()->id, [
        'started_at' => now()->subDays(2),
    ]);

    $accountBlock = createDelegateAccountBlock([
        'account' => $account,
    ]);

    Delegate::run($accountBlock);

    expect($account->delegations()->wherePivotNull('ended_at')->get())->toHaveCount(1)
        ->and($account->delegations()->get())->toHaveCount(2);
});

it('ignores existing delegation to the same pillar', function () {

    $pillar = Pillar::factory()->create([
        'name' => 'Test',
    ]);

    $account = Account::factory()->create();
    $account->delegations()->attach($pillar->id, [
        'started_at' => now()->subDays(2),
    ]);

    $accountBlock = createDelegateAccountBlock([
        'account' => $account,
    ]);

    Delegate::run($accountBlock);

    expect($account->delegations()->get())->toHaveCount(1);
});

it('dispatches the account delegated event', function () {

    Pillar::factory()->create([
        'name' => 'Test',
    ]);
    $accountBlock = createDelegateAccountBlock();

    Event::fake();

    Delegate::run($accountBlock);

    Event::assertDispatched(AccountDelegated::class);
});

it('ensure only valid pillars can be delegated to', function () {

    $accountBlock = createDelegateAccountBlock();
    $account = $accountBlock->account;

    Event::fake();
    Log::shouldReceive('error')
        ->with(
            'Contract Method Processor - Pillar: Delegate failed',
            Mockery::on(fn ($data) => $data['error'] === 'Invalid pillar')
        )
        ->once();

    Delegate::run($accountBlock);

    Event::assertNotDispatched(AccountDelegated::class);

    expect($account->delegations()->get())->toHaveCount(0);
});

it('ensure only active pillars can be delegated to', function () {

    Pillar::factory()->create([
        'name' => 'Test',
        'revoked_at' => '2020-11-24 12:00:40', // This must be before the genesis date
    ]);
    $accountBlock = createDelegateAccountBlock();
    $account = $accountBlock->account;

    Event::fake();
    Log::shouldReceive('error')
        ->with(
            'Contract Method Processor - Pillar: Delegate failed',
            Mockery::on(fn ($data) => $data['error'] === 'Pillar is revoked')
        )
        ->once();

    Delegate::run($accountBlock);

    Event::assertNotDispatched(AccountDelegated::class);

    expect($account->delegations()->get())->toHaveCount(0);
});
