<?php

declare(strict_types=1);

use App\Actions\Indexer\Pillar\Undelegate;
use App\DataTransferObjects\MockAccountBlockDTO;
use App\Enums\Nom\AccountBlockTypesEnum;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Enums\Nom\NetworkTokensEnum;
use App\Events\Indexer\Pillar\AccountUndelegated;
use App\Factories\MockAccountBlockFactory;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\ContractMethod;
use App\Models\Nom\Pillar;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\NomSeeder;
use Database\Seeders\TestGenesisSeeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

uses()->group('indexer', 'indexer-actions', 'pillar-actions');

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(NomSeeder::class);
    $this->seed(TestGenesisSeeder::class);
});

function createUndelegateAccountBlock(array $overrides = []): AccountBlock
{
    $account = $overrides['account'] ?? Account::factory()->create();

    $default = [
        'account' => $account,
        'toAccount' => load_account(EmbeddedContractsEnum::PILLAR->value),
        'token' => load_token(NetworkTokensEnum::ZNN->value),
        'amount' => (string) 0,
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Pillar', 'Undelegate'),
        'data' => '',
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('undelegates to a pillar', function () {

    $pillar = Pillar::factory()->create();
    $accountBlock = createUndelegateAccountBlock();
    $account = $accountBlock->account;

    $account->delegations()->attach($pillar->id, [
        'started_at' => $accountBlock->created_at,
    ]);

    (new Undelegate)->handle($accountBlock);

    expect($pillar->delegators()->wherePivotNotNull('ended_at')->get())->toHaveCount(1)
        ->and($account->delegations()->wherePivotNotNull('ended_at')->get())->toHaveCount(1);
});

it('dispatches the account delegated event', function () {

    $pillar = Pillar::factory()->create();
    $accountBlock = createUndelegateAccountBlock();
    $account = $accountBlock->account;

    $account->delegations()->attach($pillar->id, [
        'started_at' => $accountBlock->created_at,
    ]);

    Event::fake();

    (new Undelegate)->handle($accountBlock);

    Event::assertDispatched(AccountUndelegated::class);
});

it('ensure only active delegations can be undelegated', function () {

    $accountBlock = createDelegateAccountBlock();
    $account = $accountBlock->account;

    $account->delegations()->attach(Pillar::first()->id, [
        'started_at' => $accountBlock->created_at,
        'ended_at' => $accountBlock->created_at,
    ]);

    Event::fake();
    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Pillar: Undelegate failed',
            Mockery::on(fn ($data) => $data['error'] === 'Delegating pillar not found')
        )
        ->once();

    (new Undelegate)->handle($accountBlock);

    Event::assertNotDispatched(AccountUndelegated::class);

    expect($account->delegations()->get())->toHaveCount(1);
});
