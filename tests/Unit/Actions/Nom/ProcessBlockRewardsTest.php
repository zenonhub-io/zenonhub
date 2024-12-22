<?php

declare(strict_types=1);

use App\Actions\Nom\ProcessBlockRewards;
use App\Enums\Nom\AccountRewardTypesEnum;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Enums\Nom\NetworkTokensEnum;
use App\Models\Nom\Account;
use App\Models\Nom\AccountReward;
use App\Models\Nom\Pillar;
use App\Models\Nom\Token;
use App\Models\Nom\TokenMint;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\NomSeeder;
use Database\Seeders\TestGenesisSeeder;

uses()->group('nom', 'nom-actions', 'process-block-rewards');

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(NomSeeder::class);
    $this->seed(TestGenesisSeeder::class);
});

// TODO - Fix test
//it('doesnt process blocks to the liquidity contract', function () {
//
//    $mint = TokenMint::factory()->create([
//        'token_id' => Token::firstWhere('token_standard', NetworkTokensEnum::ZNN->value)->id,
//        'issuer_id' => Account::firstWhere('address', EmbeddedContractsEnum::PILLAR->value)->id,
//        'receiver_id' => Account::firstWhere('address', EmbeddedContractsEnum::LIQUIDITY->value)->id,
//        'amount' => 50 * config('nom.decimals'),
//    ]);
//
//    ProcessBlockRewards::run($mint);
//
//    $reward = AccountReward::first();
//    expect(AccountReward::get())->toHaveCount(0)
//        ->and($reward)->toBeNull();
//});

it('correctly assigns reward data', function () {

    $mint = TokenMint::factory()->create([
        'token_id' => Token::firstWhere('token_standard', NetworkTokensEnum::ZNN->value)->id,
        'issuer_id' => Account::firstWhere('address', EmbeddedContractsEnum::PILLAR->value)->id,
        'receiver_id' => Account::factory()->create(),
        'amount' => 50 * config('nom.decimals'),
    ]);

    ProcessBlockRewards::run($mint);

    $reward = AccountReward::with('token')->first();
    expect(AccountReward::get())->toHaveCount(1)
        ->and($reward->chain_id)->toBe($mint->chain_id)
        ->and($reward->account_block_id)->toBe($mint->account_block_id)
        ->and($reward->account_id)->toBe($mint->receiver_id)
        ->and($reward->token->token_standard)->toBe(NetworkTokensEnum::ZNN->value)
        ->and($reward->created_at->timestamp)->toBe($mint->created_at->timestamp);
});

it('correctly assigns reward token', function () {

    $mint = TokenMint::factory()->create([
        'token_id' => Token::firstWhere('token_standard', NetworkTokensEnum::QSR->value)->id,
        'issuer_id' => Account::firstWhere('address', EmbeddedContractsEnum::PILLAR->value)->id,
        'receiver_id' => Account::factory()->create(),
        'amount' => 50 * config('nom.decimals'),
    ]);

    ProcessBlockRewards::run($mint);

    $reward = AccountReward::with('token')->first();
    expect(AccountReward::get())->toHaveCount(1)
        ->and($reward->token->token_standard)->toBe(NetworkTokensEnum::QSR->value);
});

it('correctly assigns delegate rewards', function () {

    $mint = TokenMint::factory()->create([
        'token_id' => Token::firstWhere('token_standard', NetworkTokensEnum::ZNN->value)->id,
        'issuer_id' => Account::firstWhere('address', EmbeddedContractsEnum::PILLAR->value)->id,
        'receiver_id' => Account::factory()->create(),
        'amount' => 50 * config('nom.decimals'),
    ]);

    ProcessBlockRewards::run($mint);

    $reward = AccountReward::first();
    expect(AccountReward::get())->toHaveCount(1)
        ->and($reward->type)->toBe(AccountRewardTypesEnum::DELEGATE);
});

it('correctly assigns pillar rewards', function () {

    $rewardReceiver = Account::factory()->create();
    Pillar::factory()->create([
        'withdraw_account_id' => $rewardReceiver,
    ]);

    $mint = TokenMint::factory()->create([
        'token_id' => Token::firstWhere('token_standard', NetworkTokensEnum::ZNN->value)->id,
        'issuer_id' => Account::firstWhere('address', EmbeddedContractsEnum::PILLAR->value)->id,
        'receiver_id' => $rewardReceiver,
        'amount' => 50 * config('nom.decimals'),
    ]);

    ProcessBlockRewards::run($mint);

    $reward = AccountReward::first();
    expect(AccountReward::get())->toHaveCount(1)
        ->and($reward->type)->toBe(AccountRewardTypesEnum::PILLAR);
});

it('correctly assigns sentinel rewards', function () {

    $mint = TokenMint::factory()->create([
        'issuer_id' => Account::firstWhere('address', EmbeddedContractsEnum::SENTINEL->value)->id,
        'receiver_id' => Account::factory()->create(),
        'amount' => 50 * config('nom.decimals'),
    ]);

    ProcessBlockRewards::run($mint);

    $reward = AccountReward::first();
    expect(AccountReward::get())->toHaveCount(1)
        ->and($reward->type)->toBe(AccountRewardTypesEnum::SENTINEL);
});

it('correctly assigns stake rewards', function () {

    $mint = TokenMint::factory()->create([
        'token_id' => Token::firstWhere('token_standard', NetworkTokensEnum::ZNN->value)->id,
        'issuer_id' => Account::firstWhere('address', EmbeddedContractsEnum::STAKE->value)->id,
        'receiver_id' => Account::factory()->create(),
        'amount' => 50 * config('nom.decimals'),
    ]);

    ProcessBlockRewards::run($mint);

    $reward = AccountReward::with('token')->first();
    expect(AccountReward::get())->toHaveCount(1)
        ->and($reward->type)->toBe(AccountRewardTypesEnum::STAKE);
});
