<?php

declare(strict_types=1);

use App\Models\User;

uses()->group('auth', 'password-confirmation');

test('confirm password screen can be rendered', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/user/confirm-password');

    $response->assertStatus(200);
});

test('password can be confirmed', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post('/user/confirm-password', [
            'password' => 'password',
        ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();
});

test('password is not confirmed with invalid password', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post('/user/confirm-password', [
            'password' => 'wrong-password',
        ]);

    $response->assertSessionHasErrors();
});
