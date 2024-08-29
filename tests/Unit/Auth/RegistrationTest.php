<?php

declare(strict_types=1);

use Laravel\Fortify\Features;

uses()->group('auth', 'registration');

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
})->skip(fn () => ! Features::enabled(Features::registration()), 'Registration support is not enabled.');

test('registration screen cannot be rendered if support is disabled', function () {
    $response = $this->get('/register');

    $response->assertStatus(404);
})->skip(fn () => Features::enabled(Features::registration()), 'Registration support is enabled.');

test('new users can register', function () {
    $response = $this->post('/register', [
        'username' => 'Username',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'terms' => '1',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('home', absolute: false));
})->skip(fn () => ! Features::enabled(Features::registration()), 'Registration support is not enabled.');
