<?php

declare(strict_types=1);

use App\Actions\Sync\PublicNodes;
use App\Models\Nom\PublicNode;
use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    // No seeder file needed anymore. We bootstrap from https://node.zenonhub.io:35997
});

it('discovers nodes, updates versions and location, and deactivates missing ones', function (): void {
    // Existing node that should be deactivated if not rediscovered
    $stale = PublicNode::factory()->create([
        'ip' => '9.9.9.9',
        'is_active' => true,
    ]);

    Http::fake(function (Illuminate\Http\Client\Request $request) {
        $url = (string) $request->url();
        $method = strtoupper($request->method());

        // Bootstrap host - provide initial peers
        if ($method === 'POST' && str_starts_with($url, 'https://node.zenonhub.io:35997')) {
            $body = $request->data();
            if (($body['method'] ?? '') === 'stats.networkInfo') {
                return Http::response([
                    'jsonrpc' => '2.0',
                    'id' => 40,
                    'result' => [
                        'peers' => [
                            ['ip' => '1.1.1.1'],
                        ],
                    ],
                ], 200);
            }

            if (($body['method'] ?? '') === 'stats.processInfo') {
                return Http::response([
                    'jsonrpc' => '2.0',
                    'id' => 40,
                    'result' => [
                        'version' => '9.9.9',
                    ],
                ], 200);
            }
        }

        // Node RPC over HTTPS
        if ($method === 'POST' && str_starts_with($url, 'https://1.1.1.1:35997')) {
            $body = $request->data();
            if (($body['method'] ?? '') === 'stats.networkInfo') {
                return Http::response([
                    'jsonrpc' => '2.0',
                    'id' => 40,
                    'result' => [
                        'peers' => [
                            ['ip' => '2.2.2.2'],
                        ],
                    ],
                ], 200);
            }

            if (($body['method'] ?? '') === 'stats.processInfo') {
                return Http::response([
                    'jsonrpc' => '2.0',
                    'id' => 40,
                    'result' => [
                        'version' => '1.0.0',
                    ],
                ], 200);
            }
        }

        if ($method === 'POST' && str_starts_with($url, 'https://2.2.2.2:35997')) {
            $body = $request->data();
            if (($body['method'] ?? '') === 'stats.networkInfo') {
                return Http::response([
                    'jsonrpc' => '2.0',
                    'id' => 40,
                    'result' => [
                        'peers' => [],
                    ],
                ], 200);
            }

            if (($body['method'] ?? '') === 'stats.processInfo') {
                return Http::response([
                    'jsonrpc' => '2.0',
                    'id' => 40,
                    'result' => [
                        'version' => '2.0.0',
                    ],
                ], 200);
            }
        }

        // ip-api.com for location
        if ($method === 'GET' && str_starts_with($url, 'http://ip-api.com/json/')) {
            return Http::response([
                'status' => 'success',
                'isp' => 'Test ISP',
                'city' => 'Test City',
                'regionName' => 'Test Region',
                'country' => 'Testland',
                'countryCode' => 'TL',
                'lat' => 1.234,
                'lon' => 5.678,
            ], 200);
        }

        // Fallback
        return Http::response([], 404);
    });

    // Run the action
    app(PublicNodes::class)->handle();

    // Two discovered nodes
    expect(PublicNode::count())->toBeGreaterThanOrEqual(2);
    expect(PublicNode::where('ip', '1.1.1.1')->exists())->toBeTrue();
    expect(PublicNode::where('ip', '2.2.2.2')->exists())->toBeTrue();

    $node1 = PublicNode::firstWhere('ip', '1.1.1.1');
    $node2 = PublicNode::firstWhere('ip', '2.2.2.2');

    expect($node1->is_active)->toBeTrue();
    expect($node2->is_active)->toBeTrue();

    // Versions captured
    expect($node1->version)->toBe('1.0.0');
    expect($node2->version)->toBe('2.0.0');

    // Location populated
    foreach ([$node1, $node2] as $n) {
        expect($n->city)->toBe('Test City');
        expect($n->latitude)->toBeFloat();
        expect($n->longitude)->toBeFloat();
        expect($n->isp)->toBe('Test ISP');
    }

    // Stale node deactivated
    expect($stale->fresh()->is_active)->toBeFalse();
});
