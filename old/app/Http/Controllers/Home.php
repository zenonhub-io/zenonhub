<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domains\Nom\Models\AcceleratorProject;
use App\Domains\Nom\Models\Account;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Momentum;
use App\Domains\Nom\Models\Pillar;
use App\Domains\Nom\Models\Sentinel;
use App\Domains\Nom\Models\Token;
use Illuminate\Support\Number;
use Meta;

class Home
{
    public function show()
    {
        Meta::title('Zenon Hub | Explore the Zenon Network Blockchain with Ease', false)
            ->twitterImage(url('img/meta-big.png'))
            ->openGraphImage(url('img/meta-big.png'));

        return view('pages/home', [
            'stats' => [
                [
                    'name' => 'Momentums',
                    'link' => route('explorer.momentums'),
                    'value' => Number::abbreviate(Momentum::count()),
                ], [
                    'name' => 'Transactions',
                    'link' => route('explorer.transactions'),
                    'value' => Number::abbreviate(AccountBlock::count()),
                ], [
                    'name' => 'Addresses',
                    'link' => route('explorer.accounts'),
                    'value' => Number::abbreviate(Account::count()),
                ], [
                    'name' => 'Tokens',
                    'link' => route('explorer.tokens'),
                    'value' => Token::count(),
                ], [
                    'name' => 'Pillars',
                    'link' => route('explorer.accounts', ['tab' => 'pillars']),
                    'value' => Pillar::whereActive()->count(),
                ], [
                    'name' => 'Sentinels',
                    'link' => route('explorer.accounts', ['tab' => 'sentinels']),
                    'value' => Sentinel::whereActive()->count(),
                ],
            ],
            'accelerator' => AcceleratorProject::orderByLatest()->limit(8)->get(),
        ]);
    }
}
