<?php

declare(strict_types=1);

namespace App\Livewire\Explorer;

use App\Livewire\BaseTable;
use App\Models\Nom\Plasma;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PlasmaList extends BaseTable
{
    public function configure(): void
    {
        parent::configure();

        $this->setPrimaryKey('id')
            ->setDefaultSort('started_at', 'desc');

        $this->setTdAttributes(function (Column $column, $row, $columnIndex, $rowIndex) {
            if ($column->getTitle() === '') {
                return [
                    'class' => 'px-0',
                ];
            }

            return [];
        });
    }

    public function builder(): Builder
    {
        return Plasma::with('fromAccount', 'toAccount')
            ->select('*')
            ->whereActive();
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->hideIf(true),
            Column::make('From', 'from_account_id')
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.address', [
                        'row' => $row->fromAccount,
                        'alwaysShort' => true,
                    ])
                ),
            Column::make('')
                ->label(fn ($row, Column $column) => view('components.tables.columns.svg')->with([
                    'svg' => 'explorer/send',
                    'class' => 'text-success',
                    'style' => 'transform: rotate(90deg);',
                ])),
            Column::make('	Beneficiary', 'to_account_id')
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.address', [
                        'row' => $row->toAccount,
                        'alwaysShort' => true,
                    ])
                ),
            Column::make('Amount')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderByRaw('CAST(amount AS INTEGER) ' . $direction)
                )
                ->label(
                    fn ($row, Column $column) => app('qsrToken')->getFormattedAmount($row->amount) . ' ' . app('qsrToken')->symbol
                ),
            Column::make('Timestamp', 'started_at')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderBy('started_at', $direction)
                )
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.date', ['date' => $row->started_at])
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
