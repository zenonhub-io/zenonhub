<?php

declare(strict_types=1);

use App\Domains\Indexer\Actions\Liquidity\CancelLiquidityStake;
use App\Domains\Indexer\DataTransferObjects\MockAccountBlockDTO;
use App\Domains\Indexer\Events\Stake\EndStake;
use App\Domains\Indexer\Factories\MockAccountBlockFactory;
use App\Domains\Nom\Enums\AccountBlockTypesEnum;
use App\Domains\Nom\Enums\EmbeddedContractsEnum;
use App\Domains\Nom\Enums\NetworkTokensEnum;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\ContractMethod;
use App\Domains\Nom\Models\Stake;
use App\Domains\Nom\Models\Token;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\NomSeeder;
use Database\Seeders\TestGenesisSeeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

uses()->group('indexer', 'indexer-actions', 'liquidity');

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

    $account = load_account('z1qqslnf593pwpqrg5c29ezeltl8ndsrdep6yvmm');
    $token = load_token(NetworkTokensEnum::LP_ZNN_ETH->value);
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

function createCancelLiquidityStakeAccountBlock(array $overrides = []): AccountBlock
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

    $accountBlock = createCancelLiquidityStakeAccountBlock();
    $accountBlock->created_at = now();

    (new CancelLiquidityStake)->handle($accountBlock);

    $stake = Stake::first();

    expect(Stake::whereInactive()->get())->toHaveCount(1)
        ->and($stake->ended_at)->toEqual($accountBlock->created_at);
});

it('dispatches the end stake event', function () {

    $accountBlock = createCancelLiquidityStakeAccountBlock();
    $accountBlock->created_at = now();

    Event::fake();

    (new CancelLiquidityStake)->handle($accountBlock);

    Event::assertDispatched(EndStake::class);
});

it('ensures only stake owner can cancel stakes', function () {

    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Liquidity: CancelLiquidityStake failed',
            Mockery::on(fn ($data) => $data['error'] === 'Account is not stake owner')
        )
        ->once();

    $accountBlock = createCancelLiquidityStakeAccountBlock([
        'account' => load_account(config('explorer.empty_address')),
    ]);

    (new CancelLiquidityStake)->handle($accountBlock);

    expect(Stake::whereActive()->get())->toHaveCount(1);
});

it('enforces stake duration time', function () {

    Log::shouldReceive('info')
        ->with(
            'Contract Method Processor - Liquidity: CancelLiquidityStake failed',
            Mockery::on(fn ($data) => $data['error'] === 'Stake end date in the future')
        )
        ->once();

    $accountBlock = createCancelLiquidityStakeAccountBlock();

    (new CancelLiquidityStake)->handle($accountBlock);

    expect(Stake::whereActive()->get())->toHaveCount(1);
});
