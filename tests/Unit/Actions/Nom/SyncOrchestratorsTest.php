<?php

declare(strict_types=1);

use App\Actions\Sync\Orchestrators;
use App\Models\Nom\Orchestrator;
use App\Models\Nom\Pillar;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\Nom\NetworkSeeder;
use Database\Seeders\Test\PillarsSeeder;
use Illuminate\Support\Facades\Http;

uses()->group('nom', 'nom-actions', 'sync-orchestrators');

beforeEach(function () {

    $this->seed(DatabaseSeeder::class);
    $this->seed(NetworkSeeder::class);
    $this->seed(PillarsSeeder::class);

    Http::fake([
        config('services.orchestrators-status.api_url') => Http::response([
            'data' => [
                'bridge_status' => 'online',
                'online_count' => 15,
                'orchestrators' => [
                    [
                        'api_pillar_name' => 'Pillar1',
                        'error' => null,
                        'ip' => '127.0.0.1',
                        'last_checked' => '2025-10-22T14:29:53.511981',
                        'name_mismatch' => false,
                        'network_stats' => [
                            'eth' => [
                                'unwraps' => 0,
                                'wraps' => 0,
                            ],
                        ],
                        'pillar_name' => 'Pillar1',
                        'pillar_url' => 'Pillar1',
                        'producer_address' => 'z1qxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxprod1',
                        'state' => '0 (LiveState)',
                        'state_num' => 0,
                        'status' => 'online',
                    ], [

                        'api_pillar_name' => 'Pillar2',
                        'error' => null,
                        'ip' => '127.0.0.2',
                        'last_checked' => '2025-10-22T14:29:53.511981',
                        'name_mismatch' => false,
                        'network_stats' => [
                            'eth' => [
                                'unwraps' => 0,
                                'wraps' => 0,
                            ],
                        ],
                        'pillar_name' => 'Pillar2',
                        'pillar_url' => 'Pillar2',
                        'producer_address' => 'z1qxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxprod2',
                        'state' => 'Unknown',
                        'state_num' => null,
                        'status' => 'offline',
                    ],
                ],
            ],
        ]),
    ]);
});

it('syncs orchestrators from the json data', function () {

    Orchestrators::run();

    $orchestrator = Orchestrator::with('pillar', 'account')->first();
    $pillar = Pillar::firstWhere('name', 'Pillar1');

    expect(Orchestrator::count())->toBe(2)
        ->and($orchestrator->pillar->name)->toEqual('Pillar1')
        ->and($orchestrator->account->address)->toEqual('z1qxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxprod1')
        ->and($orchestrator->is_active)->toBeTrue()
        ->and($pillar->orchestrator->is_active)->toBeTrue();
});

it('correctly assigns online status', function () {

    Orchestrators::run();

    $orchestrator1 = Orchestrator::with('pillar', 'account')->find(1);
    $orchestrator2 = Orchestrator::with('pillar', 'account')->find(2);

    expect($orchestrator1->pillar->name)->toEqual('Pillar1')
        ->and($orchestrator1->is_active)->toBeTrue()
        ->and($orchestrator2->pillar->name)->toEqual('Pillar2')
        ->and($orchestrator2->is_active)->toBeFalse();
});

it('removes inactive orchestrators', function () {

    $account = load_account('z1qxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxpyllar3');
    $pillar = Pillar::firstWhere('name', 'Pillar3');

    Orchestrator::create([
        'account_id' => $account->id,
        'pillar_id' => $pillar->id,
        'is_active' => false,
    ]);

    Orchestrators::run();

    expect(Orchestrator::count())->toBe(2);
});

// TODO - This can be moved to the sync bridge status test
// it('calculates online percent correctly', function () {
//
//    Orchestrators::run();
//
//    expect(Orchestrator::getOnlinePercent())->toBe(50.0);
// });
