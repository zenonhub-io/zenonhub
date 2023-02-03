<?php

namespace App\Http\Controllers\Site;

use Cache;
use App\Http\Controllers\PageController;
use App\Models\Nom\AcceleratorProject;
use App\Models\Nom\Pillar;
use App\Models\Nom\Sentinel;

class Home extends PageController
{
    public function show()
    {
        $this->page['data']['stats'] = [
            'pillars' => Pillar::isActive()->count(),
            'sentinels' => Sentinel::isActive()->count(),
            'momentums' => short_number(Cache::get('momentum-count')),
            'transactions' => short_number(Cache::get('transaction-count')),
            'addresses' => short_number(Cache::get('address-count')),
            'delegators' => number_format((float) Cache::get('delegators-count')),
        ];
        $this->page['data']['accelerator'] = AcceleratorProject::orderByLatest()->limit(7)->get();

        return $this->render('pages/home');
    }
}
