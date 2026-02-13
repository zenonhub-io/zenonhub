<?php

declare(strict_types=1);

use App\Actions\Nom\UpdateAccountTotals;
use App\Models\Nom\Account;
use App\Models\Nom\Plasma;
use App\Models\Nom\Stake;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\Nom\NetworkSeeder;
use Database\Seeders\TestGenesisSeeder;

uses()->group('nom', 'nom-actions', 'update-account-totals');

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(NetworkSeeder::class);
    $this->seed(TestGenesisSeeder::class);
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

it('updates an accounts plasma amount', function () {

    $account = Account::factory()->create();

    Plasma::factory()->count(3)->create([
        'from_account_id' => Account::factory(),
        'to_account_id' => $account->id,
    ]);

    Plasma::factory()->ended()->create([
        'from_account_id' => Account::factory(),
        'to_account_id' => $account->id,
    ]);

    UpdateAccountTotals::run($account);

    $account->fresh();

    expect($account->plasma_amount)->toBe((string) (3 * NOM_DECIMALS))
        ->and($account->plasma()->get())->toHaveCount(4)
        ->and($account->plasma()->whereActive()->get())->toHaveCount(3)
        ->and($account->plasma()->whereInactive()->get())->toHaveCount(1);
});

it('updates an accounts reward totals', function () {

    $account = Account::factory()
        ->hasRewards(3, [
            'token_id' => app('znnToken')->id,
            'amount' => (string) (1 * NOM_DECIMALS),
        ])
        ->hasRewards(2, [
            'token_id' => app('qsrToken')->id,
            'amount' => (string) (2 * NOM_DECIMALS),
        ])
        ->create();

    UpdateAccountTotals::run($account);

    $account->fresh();

    expect($account->znn_rewards)->toBe((string) (3 * NOM_DECIMALS))
        ->and($account->qsr_rewards)->toBe((string) (4 * NOM_DECIMALS));
});
