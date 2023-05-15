<?php

namespace App\Http\Controllers\Explorer;

use App\Http\Controllers\PageController;
use App\Models\Nom\Momentum;

class Momentums extends PageController
{
    public function show()
    {
        $this->page['meta']['title'] = 'Momentums';
        $this->page['meta']['description'] = 'Momentums that have been confirmed on the Zenon Network. These are the network blocks and contain the transactions';
        $this->page['data'] = [
            'component' => 'explorer.momentums',
        ];

        return $this->render('pages/explorer/overview');
    }

    public function detail($hash)
    {
        $momentum = Momentum::findByHash($hash);

        if (! $momentum) {
            abort(404);
        }

        $this->page['meta']['title'] = 'Momentum Detail';
        $this->page['meta']['description'] = "Zenon detailed momentum info for hash {$momentum->hash}. Displays the momentum height, and a list of all its transactions";
        $this->page['data'] = [
            'component' => 'explorer.momentum',
            'momentum' => $momentum,
        ];

        return $this->render('pages/explorer/detail');
    }
}
