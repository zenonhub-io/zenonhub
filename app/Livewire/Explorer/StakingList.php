<?php

declare(strict_types=1);

namespace App\Livewire\Explorer;

use App\Livewire\BaseTable;
use App\Models\Nom\Stake;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class StakingList extends BaseTable
{
    public function configure(): void
    {
        parent::configure();

        $this->setPrimaryKey('id')
            ->setDefaultSort('started_at', 'desc');
    }

    public function builder(): Builder
    {
        return Stake::with('account', 'token')
            ->select('*')
            ->where('token_id', app('znnToken')->id)
            ->whereActive();
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->hideIf(true),
            Column::make('Address')
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.address', [
                        'row' => $row->account,
                        'alwaysShort' => true,
                    ])
                ),
            Column::make('Amount')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderByRaw('CAST(amount AS INTEGER) ' . $direction)
                )
                ->label(
                    fn ($row, Column $column) => $row->token->getFormattedAmount($row->amount) . ' ' . $row->token->symbol
                ),
            Column::make('Started', 'started_at')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderBy('started_at', $direction)
                )
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.date', ['date' => $row->started_at])
                ),
            Column::make('Lockup')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderBy('duration', $direction)
                )
                ->label(
                    fn ($row, Column $column) => $row->display_duration
                ),
            Column::make('Available')
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.date', [
                        'date' => $row->end_date,
                        'tooltip' => false,
                    ])
                ),
            Column::make('Duration')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderBy('started_at', $direction)
                )
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.date', [
                        'date' => $row->started_at,
                        'human' => true,
                        'tooltip' => false,
                        'syntax' => true,
                    ])
                ),
        ];
    }

    public function filters(): array
    {
        return [];
    }
}
