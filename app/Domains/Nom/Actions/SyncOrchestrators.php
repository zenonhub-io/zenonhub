<?php

declare(strict_types=1);

namespace App\Domains\Nom\Actions;

use App\Domains\Nom\Models\Account;
use App\Domains\Nom\Models\Orchestrator;
use App\Domains\Nom\Models\Pillar;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;

class SyncOrchestrators
{
    use AsAction;

    public string $commandSignature = 'nom:sync-orchestrators';

    public function handle(): void
    {
        $apiUrl = config('services.orchestrators-status.api_url');

        try {
            $orchestratorJson = Http::get($apiUrl)->throw()->json();
        } catch (RequestException $e) {
            Log::error('Sync Orchestrators - Failed to fetch orchestrator list', [
                'url' => $apiUrl,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        $pillarIds = collect($orchestratorJson)->map(fn ($orchestratorData) => $this->processOrchestratorData($orchestratorData)?->pillar_id)->filter();

        Orchestrator::whereNotIn('pillar_id', $pillarIds->toArray())->delete();
    }

    private function processOrchestratorData(object $orchestratorData): ?Orchestrator
    {
        $pillar = Pillar::firstWhere('name', $orchestratorData->pillar_name);
        $account = Account::firstWhere('address', $orchestratorData->stake_address);

        if (! $pillar || ! $account) {
            return null;
        }

        $orchestrator = Orchestrator::firstOrCreate([
            'pillar_id' => $pillar->id,
        ], [
            'account_id' => $account->id,
            'status' => false,
        ]);

        $orchestrator->status = $orchestratorData->online_status;
        $orchestrator->save();

        return $orchestrator;
    }
}