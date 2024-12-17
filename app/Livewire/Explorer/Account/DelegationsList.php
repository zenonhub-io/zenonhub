<?php

declare(strict_types=1);

namespace App\Livewire\Explorer\Account;

use App\Livewire\BaseTable;
use App\Models\Nom\Account;
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
                    fn ($row, Column $column) => view('components.tables.columns.pillar-link')->withRow($row)
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
        ];
    }

    public function filters(): array
    {
        return [];
    }
}
