<?php

declare(strict_types=1);

namespace App\Livewire\AcceleratorZ;

use App\Livewire\BaseTable;
use App\Models\Nom\AcceleratorProject;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class ProjectList extends BaseTable
{
    public ?string $tab = 'all';

    public string $viewMode = 'custom';

    public function configure(): void
    {
        parent::configure();

        $this->setPrimaryKey('id')
            ->setDefaultSort('created_at');

        $this->setColumnSelectDisabled();

        $this->setPerPageAccepted([6, 12, 24])
            ->setPerPage(12);
    }

    public function builder(): Builder
    {
        $query = AcceleratorProject::with(['phases'])
            ->select([
                'id',
                'hash',
                'name',
                'slug',
                'url',
                'description',
                'status',
                'znn_requested',
                'znn_price',
                'qsr_requested',
                'qsr_price',
                'total_votes',
                'total_yes_votes',
                'total_no_votes',
                'total_abstain_votes',
                'created_at',
            ]);

        if ($this->tab === 'open') {
            $query->whereOpen();
        }

        if ($this->tab === 'accepted') {
            $query->whereAccepted();
        }

        if ($this->tab === 'completed') {
            $query->whereCompleted();
        }

        if ($this->tab === 'rejected') {
            $query->whereRejected();
        }

        return $query->orderByLatest();
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->hideIf(true),
            Column::make('Name')
                ->sortable()
                ->searchable(),
            Column::make('Url')
                ->sortable()
                ->searchable(),
            Column::make('Hash')
                ->sortable()
                ->searchable(),
            Column::make('Description')
                ->sortable()
                ->searchable(),
        ];
    }

    public function filters(): array
    {
        return [];
    }

    public function renderCustomView($rows): View
    {
        return view('components.accelerator-z.grid.project-cards', [
            'projects' => $rows,
        ]);
    }
}
