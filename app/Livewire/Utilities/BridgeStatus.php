<?php

declare(strict_types=1);

namespace App\Livewire\Utilities;

use App\Models\Nom\Orchestrator;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class BridgeStatus extends Component
{
    public function render(): View
    {
        $bridgeStatus = true;
        $orchestratorStatus = false;
        $orchestratorPercentage = Orchestrator::getOnlinePercent();
        $requiredOrchestratorPercentage = Orchestrator::getRequiredOnlinePercent();

        if ($orchestratorPercentage >= $requiredOrchestratorPercentage) {
            $orchestratorStatus = true;
        }

        $indicator = 'success';
        $message = 'Bridge & Orchestrators online';

        if ($bridgeStatus && ! $orchestratorStatus) {
            $indicator = 'warning';
            $message = 'Orchestrators offline';
        }

        if (! $bridgeStatus && $orchestratorStatus) {
            $indicator = 'danger';
            $message = 'Bridge offline';
        }

        if (! $bridgeStatus && ! $orchestratorStatus) {
            $indicator = 'danger';
            $message = 'Bridge & Orchestrators offline';
        }

        // TODO - Bridge status
        return view('livewire.utilities.bridge-status', [
            'status' => $bridgeStatus,
            'message' => __($message),
            'indicator' => $indicator,
        ]);
    }
}
