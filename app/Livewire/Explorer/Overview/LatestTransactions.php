<?php

declare(strict_types=1);

namespace App\Livewire\Explorer\Overview;

use App\Models\Nom\AccountBlock;
use Livewire\Component;

class LatestTransactions extends Component
{
    public function render()
    {
        return view('livewire.explorer.overview.latest-transactions', [
            'transactions' => AccountBlock::with('token', 'account', 'toAccount', 'contractMethod')
                ->notToBurn()
                //->notContractUpdate()
                ->limit(6)
                ->latest('id')
                ->get(),
        ]);
    }
}
