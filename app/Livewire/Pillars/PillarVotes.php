<?php

declare(strict_types=1);

namespace App\Livewire\Pillars;

use App\Models\Nom\AcceleratorPhase;
use App\Models\Nom\AcceleratorProject;
use App\Models\Nom\Pillar;
use App\Models\Nom\Vote;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class PillarVotes extends DataTableComponent
{
    public string $pillarId;

    protected $model = Vote::class;

    public function configure(): void
    {
        //$this->setDebugStatus(true);

        $this->setPrimaryKey('id')
            ->setDefaultSort('created_at', 'desc');

        $this->setPerPageAccepted([10, 25, 50, 100, 150])
            ->setPerPage(25);

        $this->setSortingPillsStatus(false)
            ->setFilterPillsStatus(false)
            ->setComponentWrapperAttributes([
                'class' => 'table-responsive',
            ])->setTableAttributes([
                'class' => 'table-hover table-striped table-nowrap',
            ]);
    }

    public function builder(): Builder
    {
        return Pillar::find($this->pillarId)?->votes()
            ->with('votable')
            ->select('nom_votes.*')
            ->whereHasMorph(
                'votable',
                [AcceleratorProject::class, AcceleratorPhase::class]
            )
            ->getQuery();
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->hideIf(true),
            Column::make('Item')
                ->label(
                    fn ($row, Column $column) => view('tables.columns.pillar.votes.az-item')->withRow($row)
                ),
            Column::make('Vote')
                ->label(
                    fn ($row, Column $column) => view('tables.columns.pillar.votes.vote')->withRow($row)
                ),
            Column::make('Timestamp', 'created_at')
                ->sortable()
                ->format(
                    fn ($value, $row, Column $column) => view('tables.columns.date', ['date' => $row->created_at])
                ),
        ];
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Item')
                ->options([
                    '' => 'All',
                    'project' => 'Project',
                    'phase' => 'Phase',
                ])
                ->filter(function (Builder $builder, string $value) {
                    if ($value === 'project') {
                        $builder->where('votable_type', AcceleratorProject::class);
                    } elseif ($value === 'phase') {
                        $builder->where('votable_type', AcceleratorPhase::class);
                    }
                }),
            SelectFilter::make('Vote')
                ->options([
                    '' => 'All',
                    'yes' => 'Yes',
                    'no' => 'No',
                    'abstain' => 'Abstain',
                ])
                ->filter(function (Builder $builder, string $value) {
                    if ($value === 'yes') {
                        $builder->where('is_yes', 1);
                    } elseif ($value === 'no') {
                        $builder->where('is_no', 1);
                    } elseif ($value === 'abstain') {
                        $builder->where('is_abstain', 1);
                    }
                }),
        ];
    }
}
