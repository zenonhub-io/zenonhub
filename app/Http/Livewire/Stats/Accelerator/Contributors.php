<?php

namespace App\Http\Livewire\Stats\Accelerator;

use App\Models\Nom\Pillar;
use Illuminate\Pagination\Paginator;
use Livewire\Component;
use Livewire\WithPagination;

class Contributors extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    protected Paginator $data;

    public string $sort = 'az_engagement';

    public string $order = 'desc';

    public function render()
    {
        $this->loadContributorsData();

        return view('livewire.stats.accelerator', [
            'engagementData' => $this->data,
        ]);
    }

    private function loadContributorsData()
    {
        $this->data = Pillar::whereHas('az_votes')
            ->withCount('az_votes')
            ->orderBy($this->sort, $this->order)
            ->simplePaginate(10);
    }
}
