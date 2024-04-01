<?php

declare(strict_types=1);

namespace App\Http\Livewire\Tables;

use App\Domains\Nom\Models\Pillar;
use App\Http\Livewire\DataTableTrait;
use Livewire\Component;
use Livewire\WithPagination;

class PillarHistory extends Component
{
    use DataTableTrait;
    use WithPagination;

    public Pillar $pillar;

    protected $queryString = [
        'sort' => ['except' => 'updated_at'],
        'order' => ['except' => 'desc'],
        'search',
    ];

    public function mount()
    {
        $this->sort = request()->query('sort', 'updated_at');
        $this->order = request()->query('order', 'desc');
        $this->search = request()->query('search');
        $this->perPage = 10;
    }

    public function render()
    {
        $this->loadData();

        return view('livewire.tables.pillar-history', [
            'data' => $this->data,
        ]);
    }

    public function export()
    {
        $exportName = "pillar-history-{$this->pillar->slug}.csv";
        $export = new \App\Exports\PillarHistory($this->pillar, $this->search, $this->sort, $this->order);

        return $this->doExport($export, $exportName);
    }

    protected function initQuery()
    {
        $this->query = $this->pillar->history()
            ->where('is_reward_change', '1');
    }

    protected function filterList()
    {
        if (! $this->query) {
            return;
        }

        if ($this->search) {
            $this->query->where(function ($q) {
                $q->where('momentum_rewards', $this->search)
                    ->orWhere('delegate_rewards', $this->search);
            });
        }
    }
}
