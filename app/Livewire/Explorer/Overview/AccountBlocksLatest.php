<?php

declare(strict_types=1);

namespace App\Livewire\Explorer\Overview;

use App\Livewire\BaseComponent;
use App\Models\Nom\AccountBlock;
use Illuminate\Contracts\View\View;

class AccountBlocksLatest extends BaseComponent
{
    public function render(): View
    {
        return view('livewire.explorer.overview.account-blocks-latest', [
            'blocks' => AccountBlock::with('token', 'account', 'toAccount', 'contractMethod')
                ->notToBurn()
                ->notContractUpdate()
                ->limit(6)
                ->latest('id')
                ->get(),
        ]);
    }
}
