<?php

namespace App\Http\Livewire\Stats\Bridge;

use App\Http\Livewire\DataTableTrait;
use App\Models\Nom\Account;
use Livewire\Component;
use Livewire\WithPagination;

class Actions extends Component
{
    use WithPagination;
    use DataTableTrait;

    public function mount()
    {
        $this->perPage = 10;
    }

    public function render()
    {
        $this->loadData();

        return view('livewire.stats.bridge.actions', [
            'data' => $this->data,
        ]);
    }

    protected function initQuery()
    {
        $account = Account::findByAddress(config('zenon.bridge_admin'));
        $this->query = $account->sent_blocks()->whereNotNull('contract_method_id');
    }
}
