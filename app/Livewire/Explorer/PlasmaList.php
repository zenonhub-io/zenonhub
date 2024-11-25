<?php

declare(strict_types=1);

namespace App\Livewire\Explorer;

use App\Livewire\BaseTable;
use App\Models\Nom\Plasma;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PlasmaList extends BaseTable
{
    public ?string $tab = 'all';

    public function configure(): void
    {
        parent::configure();

        $this->setPrimaryKey('id')
            ->setDefaultSort('started_at', 'desc');
    }

    public function builder(): Builder
    {
        return Plasma::select('*')
            ->with('fromAccount', 'toAccount')->whereActive();
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->hideIf(true),
            Column::make('From', 'from_account_id')
                ->label(
                    fn ($row, Column $column) => view('tables.columns.address')->withRow($row->fromAccount)
                ),
            Column::make('')
                ->label(fn ($row, Column $column) => view('tables.columns.svg')->with([
                    'svg' => 'explorer/send',
                    'class' => 'text-success',
                    'style' => 'transform: rotate(90deg);',
                ])),
            Column::make('	Beneficiary', 'to_account_id')
                ->label(
                    fn ($row, Column $column) => view('tables.columns.address')->withRow($row->toAccount)
                ),
            Column::make('Amount')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderByRaw('CAST(amount AS INTEGER) ' . $direction)
                )
                ->label(
                    fn ($row, Column $column) => app('qsrToken')->getFormattedAmount($row->amount) . ' QSR'
                ),
            Column::make('Timestamp', 'started_at')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderBy('started_at', $direction)
                )
                ->label(
                    fn ($row, Column $column) => view('tables.columns.date', ['date' => $row->started_at])
                ),
            Column::make('Duration')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderBy('started_at', $direction)
                )
                ->label(
                    fn ($row, Column $column) => $row->display_duration
                ),
        ];
    }

    public function filters(): array
    {
        return [];
    }
}
