<?php

declare(strict_types=1);

use App\Domains\Indexer\Actions\Stake\Cancel;
use App\Domains\Indexer\DataTransferObjects\MockAccountBlockDTO;
use App\Domains\Indexer\Events\Stake\EndStake;
use App\Domains\Indexer\Factories\MockAccountBlockFactory;
use App\Domains\Nom\Enums\AccountBlockTypesEnum;
use App\Domains\Nom\Enums\EmbeddedContractsEnum;
use App\Domains\Nom\Enums\NetworkTokensEnum;
use App\Domains\Nom\Models\Account;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\ContractMethod;
use App\Domains\Nom\Models\Stake;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\NomSeeder;
use Database\Seeders\TestGenesisSeeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

uses()->group('indexer', 'indexer-actions', 'stake');

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(NomSeeder::class);
    $this->seed(TestGenesisSeeder::class);
});

function createCancelStakeAccountBlock(array $overrides = []): AccountBlock
{
    $default = [
        'account' => Account::factory()->create(),
        'toAccount' => load_account(EmbeddedContractsEnum::PLASMA->value),
        'token' => load_token(NetworkTokensEnum::ZNN->value),
        'amount' => '0',
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Plasma', 'CancelFuse'),
        'data' => '{"id":"' . hash('sha256', 'example-hash') . '"}',
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('cancels a stake', function () {

    $stake = Stake::factory()->create([
        'account_id' => Account::factory()->create(),
        'started_at' => now()->subYear()->subDay(),
    ]);
    $accountBlock = createCancelStakeAccountBlock([
        'account' => $stake->account,
        'data' => json_encode([
            'id' => $stake->hash,
        ]),
    ]);

    (new Cancel)->handle($accountBlock);

    $stake = Stake::first();

    expect(Stake::whereInactive()->get())->toHaveCount(1)
        ->and($stake->ended_at)->toEqual($accountBlock->created_at);
});

it('dispatches the end stake event', function () {

    $stake = Stake::factory()->create([
        'account_id' => Account::factory()->create(),
        'started_at' => now()->subYear()->subDay(),
    ]);
    $accountBlock = createCancelStakeAccountBlock([
        'account' => $stake->account,
        'data' => json_encode([
            'id' => $stake->hash,
        ]),
    ]);

    Event::fake();

    (new Cancel)->handle($accountBlock);

    Event::assertDispatched(EndStake::class);
});

it('ensures only stake owner can cancel stakes', function () {

    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Stake: Cancel failed',
            Mockery::on(fn ($data) => $data['error'] === 'Account is not stake owner')
        )
        ->once();

    $stake = Stake::factory()->create([
        'account_id' => Account::factory()->create(),
        'started_at' => now()->subYear()->subDay(),
    ]);
    $accountBlock = createCancelStakeAccountBlock([
        'data' => json_encode([
            'id' => $stake->hash,
        ]),
    ]);

    (new Cancel)->handle($accountBlock);

    expect(Stake::whereActive()->get())->toHaveCount(1);
});

it('enforces stake duration time', function () {

    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Stake: Cancel failed',
            Mockery::on(fn ($data) => $data['error'] === 'Stake end date in the future')
        )
        ->once();

    $stake = Stake::factory()->create([
        'account_id' => Account::factory()->create(),
        'started_at' => now(),
    ]);
    $accountBlock = createCancelStakeAccountBlock([
        'account' => $stake->account,
        'data' => json_encode([
            'id' => $stake->hash,
        ]),
    ]);

    (new Cancel)->handle($accountBlock);

    expect(Stake::whereActive()->get())->toHaveCount(1);
});
