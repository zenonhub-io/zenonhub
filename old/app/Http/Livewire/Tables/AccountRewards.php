<?php

declare(strict_types=1);

namespace App\Http\Livewire\Tables;

use App\Domains\Nom\Enums\AccountRewardTypesEnum;
use App\Domains\Nom\Models\Account;
use App\Http\Livewire\DataTableTrait;
use Livewire\Component;
use Livewire\WithPagination;

class AccountRewards extends Component
{
    use DataTableTrait;
    use WithPagination;

    public Account $account;

    protected $queryString = [
        'sort' => ['except' => 'created_at'],
        'order' => ['except' => 'desc'],
        'search',
    ];

    public function mount()
    {
        $this->sort = request()->query('sort', 'created_at');
        $this->order = request()->query('order', 'desc');
        $this->search = request()->query('search');
    }

    public function render()
    {
        $this->loadData();

        return view('livewire.tables.account-rewards', [
            'data' => $this->data,
        ]);
    }

    public function export()
    {
        $exportName = "account-rewards-{$this->account->address}.csv";
        $export = new \App\Exports\AccountRewards($this->account, $this->search, $this->sort, $this->order);

        return $this->doExport($export, $exportName);
    }

    protected function initQuery()
    {
        $this->query = $this->account->rewards();
    }

    protected function filterList()
    {
        if (! $this->query) {
            return;
        }

        if ($this->search) {
            $searchTerm = strtolower($this->search);

            if ($searchTerm === 'delegate') {
                $this->query->where('type', AccountRewardTypesEnum::DELEGATE->value);
            }

            if ($searchTerm === 'stake') {
                $this->query->where('type', AccountRewardTypesEnum::STAKE->value);
            }

            if ($searchTerm === 'pillar') {
                $this->query->where('type', AccountRewardTypesEnum::PILLAR->value);
            }

            if ($searchTerm === 'sentinel') {
                $this->query->where('type', AccountRewardTypesEnum::SENTINEL->value);
            }

            if ($searchTerm === 'liquidity') {
                $this->query->where('type', AccountRewardTypesEnum::LIQUIDITY->value);
            }

            if ($searchTerm === 'liquidity program') {
                $this->query->where('type', AccountRewardTypesEnum::LIQUIDITY_PROGRAM->value);
            }

            if ($searchTerm === 'bridge') {
                $this->query->where('type', AccountRewardTypesEnum::BRIDGE_AFFILIATE->value);
            }
        }
    }

    protected function sortList()
    {
        if (! $this->query) {
            return;
        }

        if ($this->sort === 'amount') {
            $this->query
                ->orderByRaw("{$this->sort} IS NULL ASC")
                ->orderByRaw("CAST({$this->sort} AS UNSIGNED)" . $this->order);
        } else {
            $this->query->orderBy($this->sort, $this->order);
        }
    }
}
