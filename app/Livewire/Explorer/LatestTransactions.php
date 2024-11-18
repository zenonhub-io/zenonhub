<?php

declare(strict_types=1);

namespace App\Livewire\Explorer;

use App\Models\Nom\AccountBlock;
use Livewire\Component;

class LatestTransactions extends Component
{
    public function render()
    {
        return view('livewire.explorer.latest-transactions', [
            'transactions' => AccountBlock::with('token', 'account', 'toAccount', 'contractMethod')
                ->notToEmpty()
                //->notContractUpdate()
                ->limit(6)
                ->latest('id')
                ->get(),
        ]);
    }
}
