<?php

declare(strict_types=1);

namespace App\Livewire\Explorer;

use App\Livewire\BaseTable;
use App\Models\Nom\Account;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Rappasoft\LaravelLivewireTables\Views\Column;

class AccountsList extends BaseTable
{
    public ?string $tab = 'all';

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
                DB::raw('CAST(znn_balance AS INTEGER) as formatted_znn_balance'),
                DB::raw('CAST(qsr_balance AS INTEGER) as formatted_qsr_balance'),
            )
            ->withCount('sentBlocks');

        if ($this->tab === 'contracts') {
            $query->whereEmbedded();
        }

        if ($this->tab === 'pillars') {
            $query->whereHas('pillars', function ($query) {
                $query->whereActive();
            });
        }

        if ($this->tab === 'sentinels') {
            $query->whereHas('sentinels', function ($query) {
                $query->whereActive();
            });
        }

        if ($this->tab === 'favorites' && auth()->check()) {
            $query->whereHasFavorite(auth()->user());
        }

        return $query;
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
            //            Column::make('')
            //                ->label(fn ($row, Column $column) => view('components.tables.columns.svg')->with([
            //                    'svg' => $row->is_received ? 'explorer/send' : 'explorer/unreceived',
            //                    'class' => $row->is_received ? 'text-success' : 'text-danger',
            //                    'style' => $row->is_received ? 'transform: rotate(90deg);' : null,
            //                    'tooltip' => $row->is_unreceived ? __('Unreceived') : null,
            //                ])),
            Column::make('Height')
                ->label(
                    fn ($row, Column $column) => $row->display_height
                ),
            Column::make('ZNN')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderByRaw('CAST(znn_balance AS INTEGER) ' . $direction)
                )
                ->label(
                    fn ($row, Column $column) => $znnToken->getFormattedAmount($row->znn_balance)
                ),
            Column::make('QSR')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderByRaw('CAST(qsr_balance AS INTEGER) ' . $direction)
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
