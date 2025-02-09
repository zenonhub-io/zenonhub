<?php

declare(strict_types=1);

namespace App\Livewire\Explorer\Account;

use App\Livewire\BaseTable;
use App\Models\Nom\Account;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class DelegationsList extends BaseTable
{
    public int $accountId;

    public function configure(): void
    {
        parent::configure();

        $this->setPrimaryKey('id')
            ->setDefaultSort('delegation_started_at', 'desc');
    }

    public function builder(): Builder
    {
        return Account::find($this->accountId)?->delegations()
            ->withPivot('started_at', 'ended_at', 'duration')
            ->select(
                'nom_pillars.*',
                'nom_delegations.started_at as delegation_started_at',
                'nom_delegations.ended_at as delegation_ended_at',
            )
            ->getQuery();
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')->hideIf(true),
            Column::make('Pillar')
                ->searchable(
                    fn (Builder $query, $searchTerm) => $query->where('name', 'like', "%{$searchTerm}%")
                )
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.pillar-link', [
                        'pillar' => $row,
                    ])
                ),
            Column::make('Started')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderBy('nom_delegations.started_at', $direction)
                )
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.date', ['date' => $row->delegation_started_at])
                ),
            Column::make('Ended')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderBy('nom_delegations.ended_at', $direction)
                )
                ->label(
                    fn ($row, Column $column) => $row->delegation_ended_at ? view('components.tables.columns.date', ['date' => $row->delegation_ended_at]) : null
                ),
            Column::make('Duration')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderBy('nom_delegations.ended_at', $direction)
                )
                ->label(
                    function ($row, Column $column): string {
                        $endDate = $row->delegation_ended_at ?: now();
                        $duration = Carbon::parse($endDate)->timestamp - Carbon::parse($row->delegation_started_at)->timestamp;

                        return now()->subSeconds($duration)->diffForHumans(['parts' => 2], true);
                    }
                ),
        ];
    }

    public function filters(): array
    {
        return [];
    }
}
