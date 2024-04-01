<?php

declare(strict_types=1);

namespace App\Http\Livewire\Stats\Accelerator;

use App\Domains\Nom\Enums\AcceleratorProjectStatusEnum;
use App\Domains\Nom\Models\Account;
use App\Http\Livewire\DataTableTrait;
use Livewire\Component;
use Livewire\WithPagination;

class Contributors extends Component
{
    use DataTableTrait;
    use WithPagination;

    public function mount()
    {
        $this->sort = request()->query('sort', 'znn_paid');
    }

    public function render()
    {
        $this->loadContributorsData();

        return view('livewire.stats.accelerator.contributors', [
            'data' => $this->data,
        ]);
    }

    private function loadContributorsData()
    {
        $this->data = Account::whereHas('projects')
            ->withCount([
                'projects as accepted_projects_count' => fn ($query) => $query->where('status', AcceleratorProjectStatusEnum::ACCEPTED->value),
                'projects as completed_projects_count' => fn ($query) => $query->where('status', AcceleratorProjectStatusEnum::COMPLETE->value),
                'projects as rejected_projects_count' => fn ($query) => $query->where('status', AcceleratorProjectStatusEnum::REJECTED->value),
            ])
            ->withSum(
                'projects as znn_paid', 'znn_paid'
            )
            ->withSum(
                'projects as qsr_paid', 'qsr_paid'
            )
            ->orderBy($this->sort, $this->order)
            ->paginate(10);
    }
}
