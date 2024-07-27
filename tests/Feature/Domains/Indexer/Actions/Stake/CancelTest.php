<?php

declare(strict_types=1);

use App\Domains\Indexer\Actions\Stake\Cancel;
use App\Domains\Indexer\DataTransferObjects\MockAccountBlockDTO;
use App\Domains\Indexer\Events\Stake\EndStake;
use App\Domains\Indexer\Factories\MockAccountBlockFactory;
use App\Domains\Nom\Enums\AccountBlockTypesEnum;
use App\Domains\Nom\Enums\EmbeddedContractsEnum;
use App\Domains\Nom\Enums\NetworkTokensEnum;
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

    $account = load_account('z1qqslnf593pwpqrg5c29ezeltl8ndsrdep6yvmm');
    $token = load_token(NetworkTokensEnum::ZNN->value);
    Stake::create([
        'chain_id' => $account->chain_id,
        'account_id' => $account->id,
        'token_id' => $token->id,
        'account_block_id' => 1,
        'amount' => 100 * NOM_DECIMALS,
        'duration' => '31104000',
        'hash' => hash('sha256', 'example-hash'),
        'started_at' => now()->subYear(),
        'ended_at' => null,
    ]);
});

function createCancelStakeAccountBlock(array $overrides = []): AccountBlock
{
    $default = [
        'account' => load_account('z1qqslnf593pwpqrg5c29ezeltl8ndsrdep6yvmm'),
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

    $accountBlock = createCancelStakeAccountBlock();
    $accountBlock->created_at = now();

    (new Cancel)->handle($accountBlock);

    $stake = Stake::first();

    expect(Stake::whereInactive()->get())->toHaveCount(1)
        ->and($stake->ended_at)->toEqual($accountBlock->created_at);
});

it('dispatches the end stake event', function () {

    $accountBlock = createCancelStakeAccountBlock();
    $accountBlock->created_at = now();

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

    $accountBlock = createCancelStakeAccountBlock([
        'account' => load_account(config('explorer.empty_address')),
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

    $accountBlock = createCancelStakeAccountBlock();

    (new Cancel)->handle($accountBlock);

    expect(Stake::whereActive()->get())->toHaveCount(1);
});
