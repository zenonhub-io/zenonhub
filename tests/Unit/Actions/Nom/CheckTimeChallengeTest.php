<?php

declare(strict_types=1);

use App\Actions\Nom\CheckTimeChallenge;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\TimeChallenge;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\Nom\NetworkSeeder;
use Database\Seeders\Test\PillarsSeeder;

uses()->group('nom', 'nom-actions', 'check-time-challenge');

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(NetworkSeeder::class);
    $this->seed(PillarsSeeder::class);
});

it('creates a new active time challenge', function () {
    $delay = 5;
    $accountBlock = AccountBlock::factory()->make();

    $timeChallenge = (new CheckTimeChallenge)
        ->handle($accountBlock, $accountBlock->hash, $delay);

    expect($timeChallenge->id)->toBe(1)
        ->and(Hash::check($accountBlock->hash, Hash::make($accountBlock->hash)))->toBeTrue()
        ->and($timeChallenge->start_height)->toBe($accountBlock->momentum->height)
        ->and($timeChallenge->end_height)->toBe($accountBlock->momentum->height + $delay)
        ->and($timeChallenge->is_active)->toBeTrue();
});

it('loads an existing time challenge', function () {

    $delay = 5;
    $accountBlock = AccountBlock::factory()->create();
    TimeChallenge::factory()->make([
        'hash' => $accountBlock->hash,
        'delay' => $delay,
        'start_height' => $accountBlock->momentum->height,
        'end_height' => $accountBlock->momentum->height + $delay,
        'is_active' => true,
        'created_at' => $accountBlock->momentum->created_at,
    ]);

    $timeChallenge = (new CheckTimeChallenge)
        ->handle($accountBlock, $accountBlock->hash, $delay);

    expect($timeChallenge->id)->toBe(1)
        ->and(Hash::check($accountBlock->hash, Hash::make($accountBlock->hash)))->toBeTrue()
        ->and($timeChallenge->is_active)->toBeTrue();
});

it('expires an existing time challenge', function () {

    $accountBlock = AccountBlock::factory()->create();
    $delay = 5;
    $startHeight = $accountBlock->momentum->height - $delay - 1;

    TimeChallenge::factory()->create([
        'hash' => Hash::make($accountBlock->hash),
        'start_height' => $startHeight,
        'end_height' => $startHeight + $delay,
        'is_active' => true,
    ]);

    $timeChallenge = (new CheckTimeChallenge)
        ->handle($accountBlock, $accountBlock->hash, $delay);

    expect($timeChallenge->is_active)->toBeFalse();
});

it('resets a time challenge with a non-matching hash', function () {

    $accountBlock = AccountBlock::factory()->create();
    $delay = 5;
    TimeChallenge::factory()->create([
        'hash' => Hash::make('old_hash'),
        'delay' => $delay,
        'is_active' => true,
    ]);

    $timeChallenge = (new CheckTimeChallenge)
        ->handle($accountBlock, $accountBlock->hash, $delay);

    expect($timeChallenge->start_height)->toBe($accountBlock->momentum->height)
        ->and($timeChallenge->end_height)->toBe($accountBlock->momentum->height + $delay)
        ->and($timeChallenge->is_active)->toBeTrue();
});
