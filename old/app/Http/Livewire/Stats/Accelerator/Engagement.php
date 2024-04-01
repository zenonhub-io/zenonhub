<?php

declare(strict_types=1);

namespace App\Http\Livewire\Stats\Accelerator;

use App\Domains\Nom\Models\Pillar;
use App\Http\Livewire\DataTableTrait;
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
        $this->data = Pillar::whereHas('azVotes')
            ->withCount('azVotes')
            ->orderBy($this->sort, $this->order)
            ->paginate(10);
    }
}
