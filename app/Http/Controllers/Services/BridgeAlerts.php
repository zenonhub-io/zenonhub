<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\PageController;

class BridgeAlerts extends PageController
{
    public function show()
    {
        $this->page['meta']['title'] = 'Bridge Alerts';

        return $this->render('pages/services/bridge-alerts');
    }
}
