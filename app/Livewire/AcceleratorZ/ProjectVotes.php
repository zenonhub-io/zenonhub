<?php

declare(strict_types=1);

namespace App\Livewire\AcceleratorZ;

use App\Enums\Nom\VoteEnum;
use App\Livewire\BaseTable;
use App\Models\Nom\AcceleratorProject;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class ProjectVotes extends BaseTable
{
    public int $projectId;

    public function configure(): void
    {
        parent::configure();

        $this->setPrimaryKey('id')
            ->setDefaultSort('created_at', 'desc');
    }

    public function builder(): Builder
    {
        return AcceleratorProject::find($this->projectId)?->votes()
            ->with(['votable', 'pillar'])
            ->leftJoin('nom_pillars', 'nom_votes.pillar_id', '=', 'nom_pillars.id')
            ->select([
                'nom_votes.id',
                'nom_votes.pillar_id',
                'nom_votes.vote',
                'nom_votes.created_at',
                'nom_votes.votable_type',
                'nom_votes.votable_id',
                'nom_pillars.name as pillar_name',
            ])
            ->getQuery();
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->hideIf(true),
            Column::make('Pillar')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderByRaw('pillar_name ' . $direction)
                )
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.pillar-link', [
                        'pillar' => $row->pillar,
                    ])
                ),
            Column::make('Vote')
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.vote')->withRow($row)
                ),
            Column::make('Timestamp', 'created_at')
                ->sortable()
                ->format(
                    fn ($value, $row, Column $column) => view('components.tables.columns.date', ['date' => $row->created_at])
                ),
        ];
    }

    public function filters(): array
    {
        return [
            //            SelectFilter::make('Pillar')
            //                ->options([
            //                    '' => 'All',
            //                    'project' => 'Project',
            //                    'phase' => 'Phase',
            //                ])
            //                ->filter(function (Builder $builder, string $value) {
            //                    if ($value === 'project') {
            //                        $builder->where('votable_type', AcceleratorProject::class);
            //                    } elseif ($value === 'phase') {
            //                        $builder->where('votable_type', AcceleratorPhase::class);
            //                    }
            //                }),
            SelectFilter::make('Vote')
                ->options([
                    '' => 'All',
                    'yes' => 'Yes',
                    'no' => 'No',
                    'abstain' => 'Abstain',
                ])
                ->filter(function (Builder $builder, string $value) {
                    if ($value === 'yes') {
                        $builder->where('vote', VoteEnum::YES->value);
                    } elseif ($value === 'no') {
                        $builder->where('vote', VoteEnum::NO->value);
                    } elseif ($value === 'abstain') {
                        $builder->where('vote', VoteEnum::ABSTAIN->value);
                    }
                }),
        ];
    }
}
