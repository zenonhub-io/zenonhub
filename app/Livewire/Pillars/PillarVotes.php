<?php

declare(strict_types=1);

namespace App\Livewire\Pillars;

use App\Enums\Nom\VoteEnum;
use App\Livewire\BaseTable;
use App\Models\Nom\AcceleratorPhase;
use App\Models\Nom\AcceleratorProject;
use App\Models\Nom\Pillar;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class PillarVotes extends BaseTable
{
    public int $pillarId;

    public function configure(): void
    {
        parent::configure();

        $this->setPrimaryKey('id')
            ->setDefaultSort('created_at', 'desc');
    }

    public function builder(): Builder
    {
        return Pillar::find($this->pillarId)?->votes()
            ->with(['votable'])
            ->select([
                'nom_votes.id',
                'nom_votes.pillar_id',
                'nom_votes.vote',
                'nom_votes.created_at',
                'nom_votes.votable_type',
                'nom_votes.votable_id',
            ])
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
                    fn ($row, Column $column) => view('components.tables.columns.pillar.votes.az-item')->withRow($row)
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
