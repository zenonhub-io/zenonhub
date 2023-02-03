<?php

namespace App\Http\Controllers\Accelerator;

use App\Http\Controllers\PageController;
use App\Models\Nom\AcceleratorPhase;

class Phases extends PageController
{
    public function detail($hash)
    {
        $phase = AcceleratorPhase::findByHash($hash);

        if (! $phase) {
            abort(404);
        }

        $this->page['meta']['title'] = 'Accelerator Project Phase | ' . $phase->name;
        $this->page['meta']['description'] = "A detailed overview of project phase {$phase->hash} see the funding request, description and voting status";
        $this->page['data'] = [
            'phase' => $phase
        ];

        return $this->render('pages/az/phase');
    }
}
