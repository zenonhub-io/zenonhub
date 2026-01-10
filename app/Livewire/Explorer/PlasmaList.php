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

        $this->setThAttributes(function (Column $column) {
            if ($column->getTitle() === '') {
                return [
                    'class' => 'px-0',
                ];
            }

            return [];
        });

        $this->setTdAttributes(function (Column $column, $row, $columnIndex, $rowIndex) {
            if ($column->getTitle() === '') {
                return [
                    'class' => 'py-0 pt-1 px-0',
                ];
            }

            return [];
        });
    }

    public function builder(): Builder
    {
        return Plasma::query()
            ->with([
                'fromAccount', 'toAccount', 'accountBlock',
            ])
            ->select([
                'id',
                'from_account_id',
                'to_account_id',
                'account_block_id',
                'amount',
                'started_at',
                'ended_at',
            ])
            ->whereActive();
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->hideIf(true),
            Column::make('Hash')
                ->searchable(
                    fn (Builder $query, $searchTerm) => $query->whereRelation('accountBlock', 'hash', $searchTerm)
                )
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.hash', [
                        'hash' => $row->accountBlock->hash,
                        'alwaysShort' => true,
                        'copyable' => true,
                        'link' => route('explorer.block.detail', ['hash' => $row->accountBlock->hash]),
                    ])
                ),
            Column::make('Amount')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderByRaw('CAST(amount AS SIGNED) ' . $direction)
                )
                ->label(
                    fn ($row, Column $column) => app('qsrToken')->getFormattedAmount($row->amount) . ' ' . app('qsrToken')->symbol
                ),
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
