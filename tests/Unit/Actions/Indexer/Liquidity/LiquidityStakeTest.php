<?php

declare(strict_types=1);

use App\Actions\Indexer\Liquidity\LiquidityStake;
use App\DataTransferObjects\MockAccountBlockDTO;
use App\Enums\Nom\AccountBlockTypesEnum;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Enums\Nom\NetworkTokensEnum;
use App\Events\Indexer\Stake\StartStake;
use App\Factories\MockAccountBlockFactory;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\ContractMethod;
use App\Models\Nom\Stake;
use App\Models\Nom\Token;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\NomSeeder;
use Database\Seeders\TestGenesisSeeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

uses()->group('indexer', 'indexer-actions', 'liquidity-actions');

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(NomSeeder::class);
    $this->seed(TestGenesisSeeder::class);

    Token::insert([
        'chain_id' => 1,
        'owner_id' => load_account(EmbeddedContractsEnum::BRIDGE->value)->id,
        'name' => 'wZNN-wETH-LP-ETH',
        'symbol' => 'ZNNETHLP',
        'domain' => 'zenon.network',
        'token_standard' => 'zts17d6yr02kh0r9qr566p7tg6',
        'total_supply' => '45249446791218683',
        'max_supply' => '57896044618658097711785492504343953926634992332820282019728792003956564819967',
        'decimals' => 18,
        'is_burnable' => 1,
        'is_mintable' => 1,
        'is_utility' => 1,
        'created_at' => '2023-05-17 09:27:50',
    ]);
});

function createLiquidityStakeAccountBlock(array $overrides = []): AccountBlock
{
    $default = [
        'account' => Account::factory()->create(),
        'toAccount' => load_account(EmbeddedContractsEnum::LIQUIDITY->value),
        'token' => load_token(NetworkTokensEnum::LP_ZNN_ETH->value),
        'amount' => (string) (100 * NOM_DECIMALS),
        'blockType' => AccountBlockTypesEnum::SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Liquidity', 'LiquidityStake'),
        'data' => [
            'durationInSec' => '31104000',
        ],
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('create a new liquidity stake', function () {

    $accountBlock = createLiquidityStakeAccountBlock();

    LiquidityStake::run($accountBlock);

    $stake = Stake::first();

    expect(Stake::whereActive()->get())->toHaveCount(1)
        ->and($stake->account_id)->toEqual($accountBlock->account->id)
        ->and($stake->amount)->toEqual($accountBlock->amount)
        ->and($stake->duration)->toEqual('31104000')
        ->and($stake->started_at)->toEqual($accountBlock->created_at)
        ->and($stake->ended_at)->toBeNull();
});

it('dispatches the start stake event', function () {

    $accountBlock = createLiquidityStakeAccountBlock();

    Event::fake();

    LiquidityStake::run($accountBlock);

    Event::assertDispatched(StartStake::class);
});

it('doesnt pass validation with invalid token', function () {

    $accountBlock = createLiquidityStakeAccountBlock([
        'token' => load_token(NetworkTokensEnum::QSR->value),
    ]);

    Event::fake();
    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Liquidity: LiquidityStake failed',
            Mockery::on(fn ($data) => $data['error'] === 'Invalid stake token')
        )
        ->once();

    LiquidityStake::run($accountBlock);

    Event::assertNotDispatched(StartStake::class);

    expect(Stake::whereActive()->get())->toHaveCount(0);
});

it('doesnt pass validation with invalid amount', function () {

    $accountBlock = createLiquidityStakeAccountBlock([
        'amount' => '0',
    ]);

    Event::fake();
    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Liquidity: LiquidityStake failed',
            Mockery::on(fn ($data) => $data['error'] === 'Invalid stake amount')
        )
        ->once();

    LiquidityStake::run($accountBlock);

    Event::assertNotDispatched(StartStake::class);

    expect(Stake::whereActive()->get())->toHaveCount(0);
});

it('doesnt pass validation with short duration', function () {

    $accountBlock = createLiquidityStakeAccountBlock([
        'data' => [
            'durationInSec' => '0',
        ],
    ]);

    Event::fake();
    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Liquidity: LiquidityStake failed',
            Mockery::on(fn ($data) => $data['error'] === 'Invalid stake duration')
        )
        ->once();

    LiquidityStake::run($accountBlock);

    Event::assertNotDispatched(StartStake::class);

    expect(Stake::whereActive()->get())->toHaveCount(0);
});
