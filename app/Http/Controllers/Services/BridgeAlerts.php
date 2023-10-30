<?php

namespace App\Http\Controllers\Services;

use Meta;

class BridgeAlerts
{
    public function show()
    {
        Meta::title('Bridge Alerts');

        return view('pages/services/bridge-alerts');
    }
}
