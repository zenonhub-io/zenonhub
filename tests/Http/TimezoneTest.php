<?php

declare(strict_types=1);

use App\Models\User;

uses()->group('site', 'routes', 'timezone');

test('guest can update timezone', function () {
    $response = $this->post(route('timezone.update'), [
        'timezone' => 'Europe/London',
    ]);

    $response->assertStatus(200);
});

test('logged user can update timezone', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('timezone.update'), [
        'timezone' => 'Europe/London',
    ]);

    $response->assertStatus(200);
});

test('timezone must be valid', function () {
    $response = $this->post(route('timezone.update'), [
        'timezone' => 'Fake/Zone',
    ]);

    $response->assertStatus(302);
});
