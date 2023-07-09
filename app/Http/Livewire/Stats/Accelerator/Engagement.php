<?php

namespace App\Http\Livewire\Stats\Accelerator;

use App\Http\Livewire\DataTableTrait;
use App\Models\Nom\Pillar;
use Livewire\Component;
use Livewire\WithPagination;

class Engagement extends Component
{
    use DataTableTrait;
    use WithPagination;

    public function mount()
    {
        $this->sort = request()->query('sort', 'az_engagement');
    }

    public function render()
    {
        $this->loadEngagementData();

        return view('livewire.stats.accelerator.engagement', [
            'data' => $this->data,
        ]);
    }

    private function loadEngagementData()
    {
        $this->data = Pillar::whereHas('az_votes')
            ->withCount('az_votes')
            ->orderBy($this->sort, $this->order)
            ->simplePaginate(10);
    }
}
