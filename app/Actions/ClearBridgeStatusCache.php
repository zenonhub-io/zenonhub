<?php

namespace App\Actions;

use App\Services\BridgeStatus;
use Illuminate\Support\Facades\App;

class ClearBridgeStatusCache
{
    public function execute(): void
    {
        $bridgeStatus = App::make(BridgeStatus::class);
        $bridgeStatus->clearCache();
    }
}
