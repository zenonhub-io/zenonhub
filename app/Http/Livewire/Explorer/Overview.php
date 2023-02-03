<?php

namespace App\Http\Livewire\Explorer;

use Cache;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Momentum;
use App\Models\Nom\Token;
use Illuminate\Support\Collection;
use Livewire\Component;

class Overview extends Component
{
    public Collection $momentums;
    public Collection $transactions;
    public array $stats;

    public function render()
    {
        $this->loadData();

        return view('livewire.explorer.overview');
    }

    private function loadData()
    {
        $this->momentums = Momentum::withCount('account_blocks')->limit(6)->orderBy('id', 'DESC')->get();
        $this->transactions = AccountBlock::limit(6)->orderBy('id', 'DESC')->get();

        $this->stats = [
            'momentums' => number_format(Cache::get('momentum-count')),
            'transactions' => number_format(Cache::get('transaction-count')),
            'accounts' => number_format(Cache::get('address-count')),
            'tokens' => Token::count(),
            'staked ZNN' => Cache::get('staked-znn'),
            'fused QSR' => Cache::get('fused-qsr'),
        ];
    }
}
