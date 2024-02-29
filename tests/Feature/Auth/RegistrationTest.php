<?php

use Laravel\Fortify\Features;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
})->skip(fn () => ! Features::enabled(Features::registration()), 'Registration support is not enabled.')->group('auth', 'registration');

test('registration screen cannot be rendered if support is disabled', function () {
    $response = $this->get('/register');

    $response->assertStatus(404);
})->skip(fn () => Features::enabled(Features::registration()), 'Registration support is enabled.')->group('auth', 'registration');

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
})->skip(fn () => ! Features::enabled(Features::registration()), 'Registration support is not enabled.')->group('auth', 'registration');
