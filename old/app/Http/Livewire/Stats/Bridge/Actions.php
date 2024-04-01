<?php

declare(strict_types=1);

namespace App\Http\Livewire\Stats\Bridge;

use App\Domains\Nom\Models\BridgeAdmin;
use App\Http\Livewire\DataTableTrait;
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
        $this->query = $admin->account->sentBlocks()->whereNotNull('contract_method_id');
    }
}
