<?php

declare(strict_types=1);

namespace App\Livewire\Explorer\Bridge;

use App\Livewire\BaseTable;
use App\Models\Nom\Stake;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class ZnnEthLpStakingList extends BaseTable
{
    public function configure(): void
    {
        parent::configure();

        $this->setPrimaryKey('id')
            ->setDefaultSort('started_at', 'desc');
    }

    public function builder(): Builder
    {
        return Stake::select('*')
            ->where('token_id', app('znnEthLpToken')->id)
            ->with('account', 'token')->whereActive();
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->hideIf(true),
            Column::make('Address')
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.address')->withRow($row->account)
                ),
            Column::make('Amount')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderByRaw('CAST(amount AS INTEGER) ' . $direction)
                )
                ->label(
                    fn ($row, Column $column) => app('znnEthLpToken')->getFormattedAmount($row->amount) . ' ' . app('znnEthLpToken')->symbol
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
                    fn ($row, Column $column) => view('components.tables.columns.date', ['date' => $row->end_date])
                ),
            Column::make('Duration')
                ->label(
                    fn ($row, Column $column) => $row->started_at->diffForHumans(['parts' => 2], true)
                ),
        ];
    }

    public function filters(): array
    {
        return [];
    }
}
