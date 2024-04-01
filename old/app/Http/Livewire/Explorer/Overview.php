<?php

declare(strict_types=1);

namespace App\Http\Livewire\Explorer;

use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Momentum;
use App\Domains\Nom\Models\Token;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
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
        $this->momentums = Momentum::withCount('accountBlocks')
            ->limit(9)
            ->orderBy('id', 'DESC')
            ->get();

        $this->transactions = AccountBlock::notToEmpty()
            ->notContractUpdate()
            ->orderBy('id', 'DESC')
            ->limit(6)
            ->get();

        $this->stats = [
            [
                'name' => 'Momentums',
                'link' => route('explorer.momentums'),
                'value' => number_format(Cache::get('momentum-count')),
            ], [
                'name' => 'Transactions',
                'link' => route('explorer.transactions'),
                'value' => number_format(Cache::get('transaction-count')),
            ], [
                'name' => 'Addresses',
                'link' => route('explorer.accounts'),
                'value' => number_format(Cache::get('address-count')),
            ], [
                'name' => 'Tokens',
                'link' => route('explorer.tokens'),
                'value' => Token::count(),
            ], [
                'name' => 'Staked ZNN',
                'link' => route('explorer.staking'),
                'value' => Cache::get('staked-znn'),
            ], [
                'name' => 'Fused QSR',
                'link' => route('explorer.fusions'),
                'value' => Cache::get('fused-qsr'),
            ],
        ];
    }
}
