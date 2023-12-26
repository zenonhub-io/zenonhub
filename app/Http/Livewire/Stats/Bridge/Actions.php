<?php

namespace App\Http\Livewire\Stats\Bridge;

use App\Http\Livewire\DataTableTrait;
use App\Models\Nom\BridgeAdmin;
use Livewire\Component;
use Livewire\WithPagination;

class Actions extends Component
{
    use DataTableTrait;
    use WithPagination;

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
        $admin = BridgeAdmin::getActiveAdmin();
        $this->query = $admin->account->sent_blocks()->whereNotNull('contract_method_id');
    }
}
