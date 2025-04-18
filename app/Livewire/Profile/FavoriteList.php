<?php

declare(strict_types=1);

namespace App\Livewire\Profile;

use App\Livewire\BaseTable;
use App\Models\Nom\Account;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Rappasoft\LaravelLivewireTables\Views\Column;

class FavoriteList extends BaseTable
{
    public function configure(): void
    {
        parent::configure();

        $this->setPrimaryKey('id')
            ->setDefaultSort('formatted_znn_balance', 'desc');
    }

    public function builder(): Builder
    {
        $query = Account::with('firstBlock', 'latestBlock')
            ->select(
                '*',
                DB::raw('CAST(znn_balance AS SIGNED) as formatted_znn_balance'),
                DB::raw('CAST(qsr_balance AS SIGNED) as formatted_qsr_balance'),
            )
            ->withCount('sentBlocks');

        return $query->whereHasFavorite(auth()->user());
    }

    public function columns(): array
    {
        $znnToken = app('znnToken');
        $qsrToken = app('znnToken');

        return [
            Column::make('ID', 'id')
                ->hideIf(true),
            Column::make('Address')
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.address', [
                        'row' => $row,
                        'alwaysShort' => true,
                    ])
                ),
            Column::make('Height')
                ->label(
                    fn ($row, Column $column) => $row->display_height
                ),
            Column::make(app('znnToken')->symbol)
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderByRaw('CAST(znn_balance AS SIGNED) ' . $direction)
                )
                ->label(
                    fn ($row, Column $column) => $znnToken->getFormattedAmount($row->znn_balance)
                ),
            Column::make(app('qsrToken')->symbol)
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderByRaw('CAST(qsr_balance AS SIGNED) ' . $direction)
                )
                ->label(
                    fn ($row, Column $column) => $qsrToken->getFormattedAmount($row->qsr_balance)
                ),
            Column::make('Last active', 'last_active_at')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderBy('last_active_at', $direction)
                )
                ->label(
                    fn ($row, Column $column) => $row->last_active_at ? view('components.tables.columns.date', ['date' => $row->last_active_at]) : null
                ),
        ];
    }

    public function filters(): array
    {
        return [];
    }
}
