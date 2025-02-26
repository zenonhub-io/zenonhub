<?php

declare(strict_types=1);

use App\Models\Nom\Account;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\Nom\NetworkSeeder;

uses()->group('site', 'routes');

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(NetworkSeeder::class);
});

it('rejects requests with invalid token abilities', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test-token', ['invalid-ability'])->plainTextToken;

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
        ->postJson(route('plasmaBot.fuse'), [
            'address' => Account::factory()->create()->address,
        ]);

    $response->assertStatus(403);
});

it('rejects requests without a token', function () {
    $response = $this->postJson(route('plasmaBot.fuse'), [
        'address' => Account::factory()->create()->address,
    ]);

    $response->assertStatus(401);
});
