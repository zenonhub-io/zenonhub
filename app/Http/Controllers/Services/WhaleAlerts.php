<?php

namespace App\Http\Controllers\Services;

use Meta;

class WhaleAlerts
{
    public function show()
    {
        Meta::title('Whale Alerts');

        $znnCutoff = config('bots.whale-alerts.znn_cutoff');
        $qsrCutoff = config('bots.whale-alerts.qsr_cutoff');

        return view('pages/services/whale-alerts', [
            'znnCutoff' => number_format($znnCutoff),
            'qsrCutoff' => number_format($qsrCutoff),
        ]);
    }
}
