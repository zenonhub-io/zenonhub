<?php

declare(strict_types=1);

namespace App\Livewire\Utilities;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class BridgeStatus extends Component
{
    public function render(): View
    {
        $bridgeStatusService = app(\App\Services\BridgeStatus::class);
        $bridgeOnline = $bridgeStatusService->isBridgeOnline();
        $orchestratorsOnline = $bridgeStatusService->isOrchestratorsOnline();

        $indicator = 'success';
        $message = 'Bridge & Orchestrators online';
        $usable = true;

        if ($bridgeOnline && ! $orchestratorsOnline) {
            $indicator = 'warning';
            $message = 'Orchestrators offline';
            $usable = false;
        }

        if (! $bridgeOnline && $orchestratorsOnline) {
            $indicator = 'danger';
            $message = 'Bridge offline';
            $usable = false;
        }

        if (! $bridgeOnline && ! $orchestratorsOnline) {
            $indicator = 'danger';
            $message = 'Bridge & Orchestrators offline';
            $usable = false;
        }

        return view('livewire.utilities.bridge-status', [
            'status' => $bridgeOnline,
            'message' => __($message),
            'indicator' => $indicator,
            'usable' => $usable,
        ]);
    }
}
