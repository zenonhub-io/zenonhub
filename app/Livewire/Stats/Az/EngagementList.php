<?php

declare(strict_types=1);

namespace App\Livewire\Stats\Az;

use App\Enums\Nom\VoteEnum;
use App\Livewire\BaseTable;
use App\Models\Nom\AcceleratorPhase;
use App\Models\Nom\AcceleratorProject;
use App\Models\Nom\Pillar;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class EngagementList extends BaseTable
{
    public function configure(): void
    {
        parent::configure();

        $this->setPrimaryKey('id')
            ->setDefaultSort('az_engagement', 'desc');
    }

    public function builder(): Builder
    {
        return Pillar::query()
            ->select('slug')
            ->whereNull('revoked_at')
            ->whereHas('votes', function ($query) {
                $query->whereHasMorph('votable', [AcceleratorProject::class, AcceleratorPhase::class]);
            })
            ->withCount([
                'votes as total_yes' => fn ($query) => $query->where('vote', VoteEnum::YES->value),
                'votes as total_no' => fn ($query) => $query->where('vote', VoteEnum::NO->value),
                'votes as total_abstain' => fn ($query) => $query->where('vote', VoteEnum::ABSTAIN->value),
                'votes as total_votes',
            ]);
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->hideIf(true),
            Column::make('Name')
                ->sortable()
                ->searchable()
                ->format(
                    fn ($value, $row, Column $column) => view('components.tables.columns.pillar-link')->withRow($row)
                ),
            Column::make('Engagement', 'az_engagement')
                ->sortable()
                ->format(
                    fn ($value, $row, Column $column) => $row->az_engagement . '%'
                ),
            Column::make('Avg. vote time', 'az_avg_vote_time')
                ->sortable()
                ->format(
                    fn ($value, $row, Column $column) => $row->display_az_avg_vote_time
                ),
            Column::make('Yes')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderByRaw('total_yes ' . $direction)
                )
                ->label(
                    fn ($row, Column $column) => $row->total_yes
                ),
            Column::make('No')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderByRaw('total_no ' . $direction)
                )
                ->label(
                    fn ($row, Column $column) => $row->total_no
                ),
            Column::make('Abstain')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderByRaw('total_abstain ' . $direction)
                )
                ->label(
                    fn ($row, Column $column) => $row->total_abstain
                ),
            Column::make('Total')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderByRaw('total_votes ' . $direction)
                )
                ->label(
                    fn ($row, Column $column) => $row->total_votes
                ),
        ];
    }

    public function filters(): array
    {
        return [];
    }
}
