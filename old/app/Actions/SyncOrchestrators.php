<?php

declare(strict_types=1);

namespace App\Actions;

use App\Domains\Nom\Models\Account;
use App\Domains\Nom\Models\Orchestrator;
use App\Domains\Nom\Models\Pillar;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncOrchestrators
{
    private object $json;

    public function __construct()
    {
    }

    public function execute(): void
    {
        $this->loadJson();
        $this->processOrchestrators();
        $this->calculateOnlinePercentage();
    }

    private function loadJson()
    {
        $json = Cache::get('orchestrators-list');

        try {
            $response = Http::get('http://137.184.138.90:8080/api');
            $json = $response->body();
            Cache::forever('orchestrators-list', $json);
        } catch (Throwable $throwable) {
            Log::info('Unable to load orchestrators list');
        }

        $this->json = json_decode($json, false, 512, JSON_THROW_ON_ERROR);
    }

    private function processOrchestrators(): void
    {
        $pillarIds = [];

        foreach ($this->json->pillars as $data) {

            $pillar = Pillar::firstWhere('name', $data->pillar_name);
            $account = Account::firstWhere('address', $data->stake_address);

            if (! $pillar || ! $account) {
                continue;
            }

            $pillarIds[] = $pillar->id;

            $orchestrator = Orchestrator::firstOrCreate(
                ['pillar_id' => $pillar->id],
                ['account_id' => $account->id, 'status' => false]
            );

            $orchestrator->status = $data->online_status;
            $orchestrator->save();
        }

        Orchestrator::whereNotIn('pillar_id', $pillarIds)->delete();
    }

    private function calculateOnlinePercentage(): void
    {
        $online = Orchestrator::isActive()->count() / Orchestrator::count();
        $percent = $online * 100;
        Cache::forever('orchestrators-online-percentage', $percent);
    }
}
