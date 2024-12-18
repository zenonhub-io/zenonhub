<?php

declare(strict_types=1);

namespace App\Livewire\Explorer;

use App\Livewire\BaseTable;
use App\Models\Nom\AccountBlock;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class TransactionList extends BaseTable
{
    public function configure(): void
    {
        parent::configure();

        $this->setPrimaryKey('id')
            ->setDefaultSort('created_at', 'desc');

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
        $model = new class extends AccountBlock
        {
            protected $table = 'view_latest_nom_account_blocks';
        };

        return $model::with('account', 'toAccount', 'contractMethod', 'token')
            ->select('*');
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->hideIf(true),
            Column::make('Hash')
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.hash', [
                        'hash' => $row->hash,
                        'breakpoint' => 'xxl',
                        'copyable' => true,
                        'link' => route('explorer.transaction.detail', ['hash' => $row->hash]),
                    ])
                ),
            Column::make('From')
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.address', [
                        'row' => $row->account,
                        'alwaysShort' => true,
                    ])
                ),
            Column::make('')
                ->label(fn ($row, Column $column) => view('components.tables.columns.svg')->with([
                    'svg' => $row->is_received ? 'explorer/send' : 'explorer/unreceived',
                    'class' => $row->is_received ? 'text-success' : 'text-danger',
                    'style' => $row->is_received ? 'transform: rotate(90deg);' : null,
                    'tooltip' => $row->is_unreceived ? __('Unreceived') : null,
                ])),
            Column::make('	To')
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.address', [
                        'row' => $row->toAccount,
                        'alwaysShort' => true,
                    ])
                ),
            Column::make('Amount')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderByRaw('CAST(amount AS SIGNED) ' . $direction)
                )
                ->label(
                    fn ($row, Column $column) => $row->amount > 0 ? $row->token?->getFormattedAmount($row->amount) : null
                ),
            Column::make('Token')
                ->label(function ($row, Column $column) {
                    if ($row->token && $row->amount > 0) {
                        return view('components.tables.columns.link', [
                            'link' => route('explorer.token.detail', ['zts' => $row->token->token_standard]),
                            'text' => $row->token->symbol,
                        ]);
                    }

                    return null;
                }),
            Column::make('Type')
                ->label(
                    fn ($row, Column $column) => $row->display_actual_type
                ),
            Column::make('Timestamp')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderBy('created_at', $direction)
                )
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.date', ['date' => $row->created_at])
                ),
        ];
    }

    public function filters(): array
    {
        return [];
    }
}
