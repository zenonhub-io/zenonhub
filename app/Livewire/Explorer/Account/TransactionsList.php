<?php

declare(strict_types=1);

namespace App\Livewire\Explorer\Account;

use App\Livewire\BaseTable;
use App\Models\Nom\Account;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class TransactionsList extends BaseTable
{
    public int $accountId;

    public function configure(): void
    {
        parent::configure();

        $this->setPrimaryKey('id')
            ->setDefaultSort('created_at', 'desc');

        $this->setTdAttributes(function (Column $column, $row, $columnIndex, $rowIndex) {

            if ($column->getTitle() === '') {
                return [
                    'class' => 'pe-0',
                ];
            }

            return [];
        });
    }

    public function builder(): Builder
    {
        return Account::find($this->accountId)?->blocks()
            ->with('account', 'toAccount', 'contractMethod', 'token')
            ->select('*');
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->hideIf(true),
            Column::make('TX Hash')
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.link', [
                        'link' => route('explorer.transaction.detail', ['hash' => $row->hash]),
                        'text' => short_hash($row->hash),
                    ])
                ),
            Column::make('')
                ->label(function ($row, Column $column) {
                    if ($this->accountId === $row->account_id) {
                        return view('components.tables.columns.svg')->with([
                            'svg' => 'explorer/send',
                            'class' => 'text-info',
                            'tooltip' => __('Outgoing'),
                        ]);
                    }

                    return view('components.tables.columns.svg')->with([
                        'svg' => $row->is_received ? 'explorer/send' : 'explorer/unreceived',
                        'class' => $row->is_received ? 'text-success' : 'text-danger',
                        'style' => $row->is_received ? 'transform: rotate(180deg);' : null,
                        'tooltip' => $row->is_unreceived ? __('Unreceived') : __('Incoming'),
                    ]);
                })->html(),
            Column::make('From / To')
                ->label(function ($row, Column $column) {
                    if ($this->accountId === $row->account_id) {

                        $label = __('To');
                        $address = view('components.tables.columns.address', [
                            'row' => $row->toAccount,
                            'alwaysShort' => true,
                        ])->render();

                        $svg = view('components.tables.columns.svg')->with([
                            'svg' => 'explorer/send',
                            'class' => 'text-info',
                            'tooltip' => __('Outgoing'),
                        ])->render();

                    } else {

                        $label = __('From');
                        $address = view('components.tables.columns.address', [
                            'row' => $row->account,
                            'alwaysShort' => true,
                        ])->render();

                        $svg = view('components.tables.columns.svg')->with([
                            'svg' => $row->is_received ? 'explorer/send' : 'explorer/unreceived',
                            'class' => $row->is_received ? 'text-success' : 'text-danger',
                            'style' => $row->is_received ? 'transform: rotate(180deg);' : null,
                            'tooltip' => $row->is_unreceived ? __('Unreceived') : __('Incoming'),
                        ])->render();
                    }

                    return sprintf('%s: %s', $label, $address);
                })->html(),
            Column::make('Amount')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderByRaw('CAST(amount AS INTEGER) ' . $direction)
                )
                ->label(
                    fn ($row, Column $column) => $row->token?->getFormattedAmount($row->amount)
                ),
            Column::make('Token')
                ->label(function ($row, Column $column) {
                    if (! $row->token) {
                        return null;
                    }

                    return view('components.tables.columns.link', [
                        'link' => route('explorer.token.detail', ['zts' => $row->token->token_standard]),
                        'text' => $row->token->symbol,
                    ]);
                }),
            Column::make('Type')
                ->label(
                    fn ($row, Column $column) => $row->display_type
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
        return [
            SelectFilter::make('Interactions')
                ->options([
                    '' => 'All',
                    'contracts' => 'Contracts',
                    'accounts' => 'Accounts',
                ])
                ->filter(function (Builder $builder, string $value) {
                    if ($value === 'contracts') {
                        $builder->whereRelation('toAccount', 'is_embedded_contract', '1')
                            ->notToEmpty();
                    } elseif ($value === 'accounts') {
                        $builder->whereRelation('toAccount', 'is_embedded_contract', '0')
                            ->notToEmpty();
                    }
                }),
        ];
    }
}
