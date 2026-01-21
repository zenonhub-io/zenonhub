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
            'blocks' => AccountBlock::query()
                ->select([
                    'id',
                    'account_id',
                    'to_account_id',
                    'token_id',
                    'contract_method_id',
                    'hash',
                    'amount',
                    'block_type',
                    'created_at',
                ])
                ->with([
                    'account', 'toAccount', 'contractMethod', 'token',
                ])
                ->notToBurn()
                ->notContractUpdate()
                ->limit(6)
                ->latest('id')
                ->get(),
        ]);
    }
}
