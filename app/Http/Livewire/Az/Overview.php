<?php

namespace App\Http\Livewire\Az;

use App\Http\Livewire\DataTableTrait;
use App\Models\Nom\AcceleratorProject;
use Livewire\Component;
use Livewire\WithPagination;

class Overview extends Component
{
    use WithPagination;
    use DataTableTrait;

    public $list = 'all';
    public $availableLists = [
        'all',
        'new',
        'accepted',
        'complete',
        'rejected',
    ];

    protected $queryString = [
        'search',
        'list' => ['except' => 'all'],
    ];

    public function mount()
    {
        $this->perPage = 12;
    }

    public function render()
    {
        $this->loadData();

        return view('livewire.az.overview', [
            'projects' => $this->data
        ]);
    }

    public function setList($list)
    {
        if (in_array($list, $this->availableLists)) {
            $this->list = $list;
            $this->resetPage();
        }
    }

    private function loadData()
    {
        $this->initQuery();
        $this->filterList();
        $this->sortList();
        $this->getList();
    }

    private function initQuery()
    {
        $this->query = AcceleratorProject::query();
    }

    private function filterList()
    {
        if ($this->list === 'new') {
            $this->query->isNew();
        } elseif ($this->list === 'accepted') {
            $this->query->isAccepted();
        } elseif ($this->list === 'complete') {
            $this->query->isComplete();
        } elseif ($this->list === 'rejected') {
            $this->query->isRejected();
        }

        if ($this->search) {
            $this->resetPage();
            $this->query->whereListSearch($this->search);
        }
    }

    private function sortList()
    {
        $this->query->orderByRaw('(status = 0) DESC');
        $this->query->orderByLatest();
    }

    private function getList()
    {
        $this->data = $this->query->paginate($this->perPage);
    }
}
