<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\PageController;

class WhaleAlerts extends PageController
{
    public function show()
    {
        $this->page['meta']['title'] = 'Whale Alerts';

        $znnCutoff = config('whale-alerts.znn_cutoff') / 100000000;
        $qsrCutoff = config('whale-alerts.qsr_cutoff') / 100000000;

        return $this->render('pages/services/whale-alerts', [
            'znnCutoff' => number_format($znnCutoff),
            'qsrCutoff' => number_format($qsrCutoff),
        ]);
    }
}
