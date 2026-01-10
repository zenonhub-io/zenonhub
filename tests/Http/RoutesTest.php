<?php

declare(strict_types=1);

use Database\Seeders\Nom\NetworkSeeder;

uses()->group('site', 'routes');

beforeEach(function () {
    $this->seed(NetworkSeeder::class);
});

// it('pillar pages return a successful response', function () {
//
//    $this->seed(PillarsSeeder::class);
//
//    $routes = [
//        'pillar.list' => [],
//        'pillar.detail' => [
//            'slug' => 'pillar1',
//        ],
//    ];
//
//    foreach ($routes as $routeName => $params) {
//        $route = route($routeName, $params, false);
//        $response = $this->get($route);
//
//        $this->assertSame(200, $response->status(), "Route [$routeName] returned status: " . $response->status());
//    }
// });
//
// it('sentinel pages return a successful response', function () {
//
//    $routes = [
//        'sentinel.list' => [],
//        'sentinel.detail' => [
//            'address' => '123',
//        ],
//    ];
//
//    foreach ($routes as $routeName => $params) {
//        $route = route($routeName, $params, false);
//        $response = $this->get($route);
//
//        $this->assertSame(200, $response->status(), "Route [$routeName] returned status: " . $response->status());
//    }
// });
//
// it('accelerator-z pages return a successful response', function () {
//
//    $routes = [
//        'accelerator-z.list' => [],
//        'accelerator-z.project.detail' => [
//            'hash' => '123',
//        ],
//        'accelerator-z.phase.detail' => [
//            'hash' => '123',
//        ],
//    ];
//
//    foreach ($routes as $routeName => $params) {
//        $route = route($routeName, $params, false);
//        $response = $this->get($route);
//
//        $this->assertSame(200, $response->status(), "Route [$routeName] returned status: " . $response->status());
//    }
// });
//
// it('explorer pages return a successful response', function () {
//
//    $routes = [
//        'explorer' => [],
//        'explorer.momentum.list' => [],
//        'explorer.momentum.detail' => [
//            'hash' => '123',
//        ],
//        'explorer.block.list' => [],
//        'explorer.block.detail' => [
//            'hash' => '123',
//        ],
//        'explorer.account.list' => [],
//        'explorer.account.detail' => [
//            'address' => '123',
//        ],
//        'explorer.token.list' => [],
//        'explorer.token.detail' => [
//            'zts' => '123',
//        ],
//        'explorer.bridge.list' => [],
//        'explorer.stake.list' => [],
//        'explorer.plasma.list' => [],
//    ];
//
//    foreach ($routes as $routeName => $params) {
//        $route = route($routeName, $params, false);
//        $response = $this->get($route);
//
//        $this->assertSame(200, $response->status(), "Route [$routeName] returned status: " . $response->status());
//    }
// });
//
// it('stats pages return a successful response', function () {
//
//    $routes = [
//        'stats.bridge',
//        'stats.public-nodes',
//        'stats.accelerator-z',
//    ];
//
//    foreach ($routes as $routeName) {
//        $route = route($routeName, [], false);
//
//        $response = $this->get($route);
//
//        $this->assertSame(200, $response->status(), "Route [$routeName] returned status: " . $response->status());
//    }
// });
//
// it('tool pages return a successful response', function () {
//
//    $routes = [
//        'tools.plasma-bot',
//        'tools.api-playground',
//        'tools.broadcast-message',
//        'tools.verify-signature',
//    ];
//
//    foreach ($routes as $routeName) {
//        $route = route($routeName, [], false);
//
//        $response = $this->get($route);
//
//        $this->assertSame(200, $response->status(), "Route [$routeName] returned status: " . $response->status());
//    }
// });
