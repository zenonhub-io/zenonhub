<?php

declare(strict_types=1);

use App\Domains\Indexer\DataTransferObjects\MockAccountBlockDTO;
use App\Domains\Indexer\Factories\MockAccountBlockFactory;
use App\Domains\Nom\Actions\ProcessBlockRewards;
use App\Domains\Nom\Enums\AccountBlockTypesEnum;
use App\Domains\Nom\Enums\AccountRewardTypesEnum;
use App\Domains\Nom\Enums\EmbeddedContractsEnum;
use App\Domains\Nom\Enums\NetworkTokensEnum;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\AccountReward;
use App\Domains\Nom\Models\ContractMethod;
use App\Domains\Nom\Models\Pillar;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\NomSeeder;
use Database\Seeders\TestGenesisSeeder;

uses()->group('nom', 'nom-actions', 'process-block-rewards');

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(NomSeeder::class);
    $this->seed(TestGenesisSeeder::class);
});

function createRewardAccountBlock(array $overrides = []): AccountBlock
{
    $default = [
        'account' => load_account(EmbeddedContractsEnum::PILLAR->value),
        'toAccount' => load_account(EmbeddedContractsEnum::TOKEN->value),
        'token' => load_token(NetworkTokensEnum::ZNN->value),
        'amount' => '0',
        'blockType' => AccountBlockTypesEnum::CONTRACT_SEND,
        'contractMethod' => ContractMethod::findByContractMethod('Token', 'Mint'),
        'data' => json_encode([
            'tokenStandard' => NetworkTokensEnum::ZNN->value,
            'amount' => 50 * NOM_DECIMALS,
            'receiveAddress' => 'z1qqslnf593pwpqrg5c29ezeltl8ndsrdep6yvmm',
        ]),
    ];

    $data = array_merge($default, $overrides);
    $accountBlockDTO = MockAccountBlockDTO::from($data);

    return MockAccountBlockFactory::create($accountBlockDTO);
}

it('doesnt process blocks to the liquidity contract', function () {

    $accountBlock = createRewardAccountBlock([
        'data' => json_encode([
            'tokenStandard' => NetworkTokensEnum::ZNN->value,
            'amount' => 50 * NOM_DECIMALS,
            'receiveAddress' => EmbeddedContractsEnum::LIQUIDITY->value,
        ]),
    ]);

    (new ProcessBlockRewards)->handle($accountBlock);

    $reward = AccountReward::first();
    expect(AccountReward::get())->toHaveCount(0)
        ->and($reward)->toBeNull();
});

it('correctly assigns reward data', function () {

    $rewardReceiver = load_account('z1qqslnf593pwpqrg5c29ezeltl8ndsrdep6yvmm');
    $accountBlock = createRewardAccountBlock();

    (new ProcessBlockRewards)->handle($accountBlock);

    $reward = AccountReward::with('token')->first();
    expect(AccountReward::get())->toHaveCount(1)
        ->and($reward->chain_id)->toBe($accountBlock->chain_id)
        ->and($reward->account_block_id)->toBe($accountBlock->id)
        ->and($reward->account_id)->toBe($rewardReceiver->id)
        ->and($reward->token->token_standard)->toBe(NetworkTokensEnum::ZNN->value)
        ->and($reward->created_at->timestamp)->toBe($accountBlock->created_at->timestamp);
});

it('correctly assigns reward token', function () {

    $accountBlock = createRewardAccountBlock([
        'data' => json_encode([
            'tokenStandard' => NetworkTokensEnum::QSR->value,
            'amount' => 50 * NOM_DECIMALS,
            'receiveAddress' => 'z1qqslnf593pwpqrg5c29ezeltl8ndsrdep6yvmm',
        ]),
    ]);

    (new ProcessBlockRewards)->handle($accountBlock);

    $reward = AccountReward::with('token')->first();
    expect(AccountReward::get())->toHaveCount(1)
        ->and($reward->token->token_standard)->toBe(NetworkTokensEnum::QSR->value);
});

it('correctly assigns delegate rewards', function () {

    $accountBlock = createRewardAccountBlock([
        'account' => load_account(EmbeddedContractsEnum::PILLAR->value),
    ]);

    (new ProcessBlockRewards)->handle($accountBlock);

    $reward = AccountReward::first();
    expect(AccountReward::get())->toHaveCount(1)
        ->and($reward->type)->toBe(AccountRewardTypesEnum::DELEGATE);
});

it('correctly assigns pillar rewards', function () {

    $accountBlock = createRewardAccountBlock([
        'account' => load_account(EmbeddedContractsEnum::PILLAR->value),
    ]);
    $rewardReceiver = load_account('z1qqslnf593pwpqrg5c29ezeltl8ndsrdep6yvmm');
    $pillar = Pillar::first();
    $pillar->withdraw_account_id = $rewardReceiver->id;
    $pillar->save();

    (new ProcessBlockRewards)->handle($accountBlock);

    $reward = AccountReward::first();
    expect(AccountReward::get())->toHaveCount(1)
        ->and($reward->type)->toBe(AccountRewardTypesEnum::PILLAR);
});

it('correctly assigns sentinel rewards', function () {

    $accountBlock = createRewardAccountBlock([
        'account' => load_account(EmbeddedContractsEnum::SENTINEL->value),
    ]);

    (new ProcessBlockRewards)->handle($accountBlock);

    $reward = AccountReward::first();
    expect(AccountReward::get())->toHaveCount(1)
        ->and($reward->type)->toBe(AccountRewardTypesEnum::SENTINEL);
});

it('correctly assigns stake rewards', function () {

    $accountBlock = createRewardAccountBlock([
        'account' => load_account(EmbeddedContractsEnum::STAKE->value),
    ]);

    (new ProcessBlockRewards)->handle($accountBlock);

    $reward = AccountReward::with('token')->first();
    expect(AccountReward::get())->toHaveCount(1)
        ->and($reward->type)->toBe(AccountRewardTypesEnum::STAKE);
});