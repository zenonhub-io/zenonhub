<?php

declare(strict_types=1);

namespace App\Actions\Sync;

use App\Models\Nom\Orchestrator;
use App\Models\Nom\Pillar;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;

class Orchestrators
{
    use AsAction;

    public string $commandSignature = 'sync:orchestrators';

    public function handle(): void
    {
        $apiUrl = config('services.orchestrators-status.api_url');
        $apiToken = config('services.orchestrators-status.api_token');

        if (! $apiUrl) {
            return;
        }

        try {
            $orchestratorJson = Http::withHeader('X-API-Key', $apiToken)
                ->get($apiUrl)
                ->throw()
                ->json('data.orchestrators');
        } catch (RequestException $e) {
            Log::error('Sync Orchestrators - Failed to fetch orchestrator list', [
                'url' => $apiUrl,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        $pillarIds = collect($orchestratorJson)
            ->map(fn ($orchestratorData) => $this->processOrchestratorData($orchestratorData)?->pillar_id)
            ->filter();

        Orchestrator::whereNotIn('pillar_id', $pillarIds->toArray())->delete();
    }

    private function processOrchestratorData(array $orchestratorData): ?Orchestrator
    {
        $pillar = Pillar::firstWhere('name', $orchestratorData['pillar_name']);

        if (! $pillar) {
            return null;
        }

        $orchestrator = Orchestrator::firstOrCreate([
            'pillar_id' => $pillar->id,
        ], [
            'account_id' => $pillar->producer_account_id,
        ]);

        $orchestrator->is_active = $orchestratorData['status'] === 'online' ? 1 : 0;
        $orchestrator->save();

        return $orchestrator;
    }
}
