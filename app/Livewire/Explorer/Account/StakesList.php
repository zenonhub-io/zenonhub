<?php

declare(strict_types=1);

namespace App\Livewire\Explorer\Account;

use App\Livewire\BaseTable;
use App\Models\Nom\Account;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class StakesList extends BaseTable
{
    public int $accountId;

    public function configure(): void
    {
        parent::configure();

        $this->setPrimaryKey('id')
            ->setDefaultSort('started_at', 'desc');
    }

    public function builder(): Builder
    {
        return Account::find($this->accountId)?->stakes()
            ->with(['token'])
            ->select('*')
            ->getQuery();
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')->hideIf(true),
            Column::make('Amount')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderByRaw('CAST(amount AS SIGNED) ' . $direction)
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
                ->label(
                    fn ($row, Column $column) => $row->display_current_duration
                ),
            Column::make('Ended', 'ended_at')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderBy('ended_at', $direction)
                )
                ->label(
                    fn ($row, Column $column) => $row->ended_at ? view('components.tables.columns.date', ['date' => $row->ended_at]) : null
                ),
        ];
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Status')
                ->options([
                    '' => 'All',
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                ])
                ->filter(function (Builder $builder, string $value) {
                    if ($value === 'active') {
                        $builder->whereNull('ended_at');
                    } elseif ($value === 'inactive') {
                        $builder->whereNotNull('ended_at');
                    }
                }),
            SelectFilter::make('Token')
                ->options([
                    '' => 'All',
                    'znn' => 'ZNN',
                    'znnethlp' => 'ZNN-ETH-LP',
                ])
                ->filter(function (Builder $builder, string $value) {
                    if ($value === 'znn') {
                        $builder->where('token_id', app('znnToken')->id);
                    } elseif ($value === 'znnethlp') {
                        $builder->where('token_id', app('znnEthLpToken')->id);
                    }
                }),
        ];
    }
}
