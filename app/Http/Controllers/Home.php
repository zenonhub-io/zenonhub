<?php

namespace App\Http\Controllers;

use App\Models\Nom\AcceleratorProject;
use App\Models\Nom\Pillar;
use App\Models\Nom\Sentinel;
use App\Models\Nom\Token;
use Illuminate\Support\Facades\Cache;
use Meta;

class Home
{
    public function show()
    {
        Meta::title('Zenon Hub | Explore the Zenon Network Blockchain with Ease', false)
            ->twitterImage('/img/meta-big.png')
            ->openGraphImage('/img/meta-big.png');

        return view('pages/home', [
            'stats' => [
                [
                    'name' => 'Momentums',
                    'link' => route('explorer.momentums'),
                    'value' => short_number(Cache::get('momentum-count')),
                ], [
                    'name' => 'Transactions',
                    'link' => route('explorer.transactions'),
                    'value' => short_number(Cache::get('transaction-count')),
                ], [
                    'name' => 'Addresses',
                    'link' => route('explorer.accounts'),
                    'value' => short_number(Cache::get('address-count')),
                ], [
                    'name' => 'Tokens',
                    'link' => route('explorer.tokens'),
                    'value' => Token::count(),
                ], [
                    'name' => 'Pillars',
                    'link' => route('explorer.accounts', ['tab' => 'pillars']),
                    'value' => Pillar::isActive()->count(),
                ], [
                    'name' => 'Sentinels',
                    'link' => route('explorer.accounts', ['tab' => 'sentinels']),
                    'value' => Sentinel::isActive()->count(),
                ],
            ],
            'accelerator' => AcceleratorProject::orderByLatest()->limit(8)->get(),
        ]);
    }
}
