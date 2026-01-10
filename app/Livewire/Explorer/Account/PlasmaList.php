<?php

declare(strict_types=1);

namespace App\Livewire\Explorer\Account;

use App\Livewire\BaseTable;
use App\Models\Nom\Account;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class PlasmaList extends BaseTable
{
    public int $accountId;

    public function configure(): void
    {
        parent::configure();

        $this->setPrimaryKey('id')
            ->setDefaultSort('started_at', 'desc');

        $this->setThAttributes(function (Column $column) {
            if ($column->getTitle() === '') {
                return [
                    'class' => 'pe-0',
                ];
            }

            return [];
        });

        $this->setTdAttributes(function (Column $column, $row, $columnIndex, $rowIndex) {
            if ($column->getTitle() === '') {
                return [
                    'class' => 'py-0 pt-1 pe-0',
                ];
            }

            return [];
        });
    }

    public function builder(): Builder
    {
        return Account::find($this->accountId)?->plasma()
            ->with(['toAccount', 'fromAccount', 'accountBlock'])
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
            Column::make('ID', 'id')->hideIf(true),
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
            Column::make('')
                ->label(function ($row, Column $column) {
                    $receiver = $this->accountId === $row->to_account_id;

                    return view('components.tables.columns.svg')->with([
                        'svg' => 'explorer/send',
                        'class' => $receiver ? 'text-success' : 'text-info',
                        'style' => $receiver ? 'transform: rotate(180deg);' : null,
                        'tooltip' => $receiver ? __('Received') : __('Sent'),
                    ]);
                })->html(),
            Column::make('From / To')
                ->label(function ($row, Column $column) {
                    if ($row->to_account_id === $row->from_account_id) {
                        $label = __('From');
                        $address = view('components.tables.columns.address', [
                            'row' => $row->toAccount,
                            'alwaysShort' => true,
                        ])->render();
                    } elseif ($this->accountId === $row->from_account_id) {
                        $label = __('To');
                        $address = view('components.tables.columns.address', [
                            'row' => $row->toAccount,
                            'alwaysShort' => true,
                        ])->render();
                    } else {
                        $label = __('From');
                        $address = view('components.tables.columns.address', [
                            'row' => $row->fromAccount,
                            'alwaysShort' => true,
                        ])->render();
                    }

                    return sprintf('%s: %s', $label, $address);
                })->html(),
            Column::make('Started', 'started_at')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderBy('started_at', $direction)
                )
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.date', ['date' => $row->started_at])
                ),
            Column::make('Duration')
                ->label(
                    fn ($row, Column $column) => $row->display_duration
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
        ];
    }
}
