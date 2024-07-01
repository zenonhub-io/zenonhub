<?php

declare(strict_types=1);

use App\Domains\Nom\Actions\SyncPublicNodes;
use App\Domains\Nom\Models\PublicNode;
use App\Domains\Nom\Models\PublicNodeHistory;
use Illuminate\Support\Facades\Http;

uses()->group('nom', 'nom-actions', 'sync-public-nodes');

beforeEach(function () {
    Http::fake([
        config('services.public-rpc-nodes.api_url') => Http::response([
            [
                'ip' => '127.0.0.1',
                'znnd' => 'v0.0.5',
                'provider' => 'ovh.us',
                'city' => 'Reston',
                'region' => 'Virginia',
                'country' => 'United States of America',
            ], [
                'ip' => '127.0.0.2',
                'znnd' => 'v0.0.7',
                'provider' => 'DIGITALOCEAN-ASN, US',
                'city' => 'London',
                'region' => 'England',
                'country' => 'United Kingdom of Great Britain and Northern Ireland',
            ],
        ]),
        'http://ip-api.com/json/*' => Http::sequence()
            ->push([
                'status' => 'success',
                'country' => 'United States',
                'countryCode' => 'US',
                'region' => 'VA',
                'regionName' => 'Virginia',
                'city' => 'Reston',
                'zip' => '20190',
                'lat' => 38.958,
                'lon' => -77.3592,
                'timezone' => 'America/New_York',
                'isp' => 'OVH SAS',
                'org' => 'OVH US LLC',
                'as' => 'AS16276 OVH SAS',
                'query' => '127.0.0.1',
            ])
            ->push([
                'status' => 'success',
                'country' => 'United Kingdom',
                'countryCode' => 'GB',
                'region' => 'ENG',
                'regionName' => 'England',
                'city' => 'Slough',
                'zip' => 'SL1',
                'lat' => 51.5368,
                'lon' => -0.6718,
                'timezone' => 'Europe/London',
                'isp' => 'DigitalOcean, LLC',
                'org' => 'DigitalOcean, LLC',
                'as' => 'AS14061 DigitalOcean, LLC',
                'query' => '165.232.105.129',
            ]),
    ]);
});

it('sync public nodes from the json data', function () {

    (new SyncPublicNodes)->handle();

    $publicNode = PublicNode::first();

    expect(PublicNode::count())->toBe(2)
        ->and($publicNode->ip)->toEqual('127.0.0.1')
        ->and($publicNode->version)->toEqual('v0.0.5')
        ->and($publicNode->is_active)->toBeTrue();
});

it('populates location data for the node', function () {

    (new SyncPublicNodes)->handle();

    $publicNode = PublicNode::first();

    expect(PublicNode::count())->toBe(2)
        ->and($publicNode->isp)->toEqual('OVH SAS')
        ->and($publicNode->city)->toEqual('Reston')
        ->and($publicNode->region)->toEqual('Virginia')
        ->and($publicNode->country)->toEqual('United States')
        ->and($publicNode->country_code)->toEqual('US')
        ->and($publicNode->latitude)->toEqual(38.958)
        ->and($publicNode->longitude)->toEqual(-77.3592);
});

it('deactivates old nodes', function () {

    $publicNode = PublicNode::create([
        'ip' => '127.0.0.0',
        'discovered_at' => now(),
    ]);

    (new SyncPublicNodes)->handle();

    expect(PublicNode::count())->toBe(3)
        ->and(PublicNode::whereActive()->count())->toEqual(2)
        ->and($publicNode->fresh()->is_active)->toBeFalse();
});

it('creates public node history', function () {

    (new SyncPublicNodes)->handle();

    $history = PublicNodeHistory::first();

    expect(PublicNodeHistory::count())->toBe(1)
        ->and($history->node_count)->toEqual(2)
        ->and($history->unique_versions)->toEqual(2)
        ->and($history->unique_isps)->toEqual(2)
        ->and($history->unique_cities)->toEqual(2)
        ->and($history->unique_countries)->toEqual(2);
});
