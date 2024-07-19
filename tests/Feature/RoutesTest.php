<?php

declare(strict_types=1);

use Database\Seeders\Nom\Test\PillarsSeeder;
use Database\Seeders\NomSeeder;

uses()->group('site', 'routes');

beforeEach(function () {
    $this->seed(NomSeeder::class);
});

it('basic pages return a successful response', function () {

    $routes = [
        'home',
        'terms',
        'policy',
        'donate',
        'sponsor',
    ];

    foreach ($routes as $routeName) {
        $route = route($routeName, [], false);

        $response = $this->get($route);

        $this->assertSame(200, $response->status(), "Route [$routeName] returned status: " . $response->status());
    }
});

it('pillar pages return a successful response', function () {

    $this->seed(PillarsSeeder::class);

    $routes = [
        'pillars' => [],
        'pillar.detail' => [
            'slug' => 'pillar1',
        ],
    ];

    foreach ($routes as $routeName => $params) {
        $route = route($routeName, $params, false);
        $response = $this->get($route);

        $this->assertSame(200, $response->status(), "Route [$routeName] returned status: " . $response->status());
    }
});

it('sentinel pages return a successful response', function () {

    $routes = [
        'sentinels' => [],
        'sentinel.detail' => [
            'address' => '123',
        ],
    ];

    foreach ($routes as $routeName => $params) {
        $route = route($routeName, $params, false);
        $response = $this->get($route);

        $this->assertSame(200, $response->status(), "Route [$routeName] returned status: " . $response->status());
    }
});

it('accelerator-z pages return a successful response', function () {

    $routes = [
        'accelerator-z' => [],
        'accelerator-z.project.detail' => [
            'hash' => '123',
        ],
        'accelerator-z.phase.detail' => [
            'hash' => '123',
        ],
    ];

    foreach ($routes as $routeName => $params) {
        $route = route($routeName, $params, false);
        $response = $this->get($route);

        $this->assertSame(200, $response->status(), "Route [$routeName] returned status: " . $response->status());
    }
});

it('explorer pages return a successful response', function () {

    $routes = [
        'explorer' => [],
        'explorer.momentums' => [],
        'explorer.momentum.detail' => [
            'hash' => '123',
        ],
        'explorer.transactions' => [],
        'explorer.transaction.detail' => [
            'hash' => '123',
        ],
        'explorer.accounts' => [],
        'explorer.account.detail' => [
            'address' => '123',
        ],
        'explorer.tokens' => [],
        'explorer.token.detail' => [
            'zts' => '123',
        ],
        'explorer.bridge' => [],
        'explorer.stakes' => [],
        'explorer.plasma' => [],
    ];

    foreach ($routes as $routeName => $params) {
        $route = route($routeName, $params, false);
        $response = $this->get($route);

        $this->assertSame(200, $response->status(), "Route [$routeName] returned status: " . $response->status());
    }
});

it('stats pages return a successful response', function () {

    $routes = [
        'stats.bridge',
        'stats.public-nodes',
        'stats.accelerator-z',
    ];

    foreach ($routes as $routeName) {
        $route = route($routeName, [], false);

        $response = $this->get($route);

        $this->assertSame(200, $response->status(), "Route [$routeName] returned status: " . $response->status());
    }
});

it('tool pages return a successful response', function () {

    $routes = [
        'tools.plasma-bot',
        'tools.api-playground',
        'tools.broadcast-message',
        'tools.verify-signature',
    ];

    foreach ($routes as $routeName) {
        $route = route($routeName, [], false);

        $response = $this->get($route);

        $this->assertSame(200, $response->status(), "Route [$routeName] returned status: " . $response->status());
    }
});
