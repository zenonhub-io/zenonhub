<?php

declare(strict_types=1);

use App\Actions\Nom\UpdateAccountTotals;
use App\Enums\Nom\NetworkTokensEnum;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Plasma;
use App\Models\Nom\Stake;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\NomSeeder;
use Database\Seeders\TestGenesisSeeder;

uses()->group('nom', 'nom-actions', 'update-account-totals');

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(NomSeeder::class);
    $this->seed(TestGenesisSeeder::class);
});

it('updates an accounts current balance', function () {

    $token = load_token(NetworkTokensEnum::ZNN->value);

    $sender = Account::factory()->create();
    $receiver = Account::factory()->create();

    AccountBlock::factory()->count(5)->create([
        'account_id' => $sender->id,
        'to_account_id' => $receiver->id,
        'token_id' => $token->id,
        'amount' => (string) (1 * NOM_DECIMALS),
    ]);

    UpdateAccountTotals::run($receiver);

    $receiver->fresh();
    $receiverBalance = $receiver->tokens()
        ->where('token_id', $token->id)
        ->first();

    expect($receiverBalance)->not->toBeNull()
        ->and($receiverBalance?->pivot->balance)->toBe((string) (5 * NOM_DECIMALS));
});

it('updates an accounts current znn balance', function () {

    $token = load_token(NetworkTokensEnum::ZNN->value);

    $sender = Account::factory()->create();
    $receiver = Account::factory()->create();

    AccountBlock::factory()->count(5)->create([
        'account_id' => $sender->id,
        'to_account_id' => $receiver->id,
        'token_id' => $token->id,
        'amount' => (string) (1 * NOM_DECIMALS),
    ]);

    UpdateAccountTotals::run($receiver);

    $receiver->fresh();

    expect($receiver->znn_balance)->toBe((string) (5 * NOM_DECIMALS));
});

it('updates an accounts znn send and received totals', function () {

    $token = load_token(NetworkTokensEnum::ZNN->value);

    $sender = Account::factory()->create();
    $receiver = Account::factory()->create();

    AccountBlock::factory()->count(3)->create([
        'account_id' => $sender->id,
        'to_account_id' => $receiver->id,
        'token_id' => $token->id,
        'amount' => (string) (1 * NOM_DECIMALS),
    ]);

    AccountBlock::factory()->count(2)->create([
        'account_id' => $receiver->id,
        'to_account_id' => $sender->id,
        'token_id' => $token->id,
        'amount' => (string) (1 * NOM_DECIMALS),
    ]);

    UpdateAccountTotals::run($receiver);

    $receiver->fresh();

    expect($receiver->znn_balance)->toBe((string) (1 * NOM_DECIMALS))
        ->and($receiver->znn_sent)->toBe((string) (2 * NOM_DECIMALS))
        ->and($receiver->znn_received)->toBe((string) (3 * NOM_DECIMALS));
});

it('updates an accounts current qsr balance', function () {

    $token = load_token(NetworkTokensEnum::QSR->value);

    $sender = Account::factory()->create();
    $receiver = Account::factory()->create();

    AccountBlock::factory()->count(5)->create([
        'account_id' => $sender->id,
        'to_account_id' => $receiver->id,
        'token_id' => $token->id,
        'amount' => (string) (1 * NOM_DECIMALS),
    ]);

    UpdateAccountTotals::run($receiver);

    $receiver->fresh();

    expect($receiver->qsr_balance)->toBe((string) (5 * NOM_DECIMALS));
});

it('updates an accounts qsr send and received totals', function () {

    $token = load_token(NetworkTokensEnum::QSR->value);

    $sender = Account::factory()->create();
    $receiver = Account::factory()->create();

    AccountBlock::factory()->count(3)->create([
        'account_id' => $sender->id,
        'to_account_id' => $receiver->id,
        'token_id' => $token->id,
        'amount' => (string) (1 * NOM_DECIMALS),
    ]);

    AccountBlock::factory()->count(2)->create([
        'account_id' => $receiver->id,
        'to_account_id' => $sender->id,
        'token_id' => $token->id,
        'amount' => (string) (1 * NOM_DECIMALS),
    ]);

    UpdateAccountTotals::run($receiver);

    $receiver->fresh();

    expect($receiver->qsr_balance)->toBe((string) (1 * NOM_DECIMALS))
        ->and($receiver->qsr_sent)->toBe((string) (2 * NOM_DECIMALS))
        ->and($receiver->qsr_received)->toBe((string) (3 * NOM_DECIMALS));
});

it('accounts for an addresses genesis znn balance', function () {

    $token = load_token(NetworkTokensEnum::ZNN->value);

    $sender = Account::factory()->create();
    $receiver = Account::factory()->create();
    $receiver->genesis_znn_balance = (string) (5 * NOM_DECIMALS);
    $receiver->save();

    AccountBlock::factory()->count(5)->create([
        'account_id' => $sender->id,
        'to_account_id' => $receiver->id,
        'token_id' => $token->id,
        'amount' => (string) (1 * NOM_DECIMALS),
    ]);

    UpdateAccountTotals::run($receiver);

    $receiver->fresh();

    expect($receiver->znn_balance)->toBe((string) (10 * NOM_DECIMALS));
});

it('accounts for an addresses genesis qsr balance', function () {

    $token = load_token(NetworkTokensEnum::QSR->value);

    $sender = Account::factory()->create();
    $receiver = Account::factory()->create();
    $receiver->genesis_qsr_balance = (string) (5 * NOM_DECIMALS);
    $receiver->save();

    AccountBlock::factory()->count(5)->create([
        'account_id' => $sender->id,
        'to_account_id' => $receiver->id,
        'token_id' => $token->id,
        'amount' => (string) (1 * NOM_DECIMALS),
    ]);

    UpdateAccountTotals::run($receiver);

    $receiver->fresh();

    expect($receiver->qsr_balance)->toBe((string) (10 * NOM_DECIMALS));
});

it('updates an accounts staked znn', function () {

    $account = Account::factory()
        ->has(Stake::factory()->count(5), 'stakes')
        ->create();

    Stake::factory()->ended()->create([
        'account_id' => $account->id,
    ]);

    UpdateAccountTotals::run($account);

    $account->fresh();

    expect($account->znn_staked)->toBe((string) (5 * NOM_DECIMALS))
        ->and($account->stakes)->toHaveCount(6)
        ->and($account->stakes()->whereActive()->get())->toHaveCount(5)
        ->and($account->stakes()->whereinactive()->get())->toHaveCount(1);
});

it('updates an accounts fused qsr', function () {

    $account = Account::factory()
        ->has(Plasma::factory()->count(5), 'fusions')
        ->create();

    Plasma::factory()->ended()->create([
        'from_account_id' => $account->id,
        'to_account_id' => $account->id,
    ]);

    UpdateAccountTotals::run($account);

    $account->fresh();

    expect($account->qsr_fused)->toBe((string) (5 * NOM_DECIMALS))
        ->and($account->fusions)->toHaveCount(6)
        ->and($account->fusions()->whereActive()->get())->toHaveCount(5)
        ->and($account->fusions()->whereinactive()->get())->toHaveCount(1);
});
