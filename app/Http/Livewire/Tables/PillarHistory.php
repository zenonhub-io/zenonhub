<?php

namespace App\Http\Livewire\Tables;

use App\Models\Nom\Pillar;
use Livewire\Component;

class PillarHistory extends Component
{
    use \Livewire\WithPagination;
    use \App\Http\Livewire\DataTableTrait;

    public Pillar $pillar;
    protected $queryString = [
        'sort' => ['except' => 'updated_at'],
        'order' => ['except' => 'desc']
    ];

    public function mount()
    {
        $this->sort = request()->query('sort', 'updated_at');
        $this->perPage = 10;
    }

    public function render()
    {
        $this->loadData();

        return view('livewire.tables.pillar-history', [
            'data' => $this->data
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
                $q->where('give_momentum_reward_percentage', $this->search)
                    ->orWhere('give_delegate_reward_percentage', $this->search);
            });
            $this->resetPage();
        }
    }
}
