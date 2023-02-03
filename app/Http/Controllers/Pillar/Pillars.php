<?php

namespace App\Http\Controllers\Pillar;

use App\Http\Controllers\PageController;
use App\Models\Nom\Pillar;

class Pillars extends PageController
{
    public function show()
    {
        $this->page['meta']['title'] = 'Network Pillars';
        $this->page['meta']['description'] = 'The backbone of Network of Momentum, Pillars participate both in the consensus protocol and in the governance framework';

        return $this->render('pages/pillars/overview');
    }

    public function detail($slug)
    {
        $pillar = Pillar::findBySlug($slug);

        if (! $pillar) {
            abort(404);
        }

        $this->page['meta']['title'] = 'Pillar | ' . $pillar->name;
        $this->page['meta']['description'] = "The pillar page shows an overview of the pillars on-chain stats and activity including lists of delegators, votes, updates and messages";
        $this->page['data'] = [
            'pillar' => $pillar
        ];

        return $this->render('pages/pillars/pillar');
    }
}
