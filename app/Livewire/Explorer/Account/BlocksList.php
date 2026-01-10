<?php

declare(strict_types=1);

namespace App\Livewire\Explorer\Account;

use App\Factories\AccountBlockModelFactory;
use App\Livewire\BaseTable;
use App\Models\Nom\Account;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class BlocksList extends BaseTable
{
    public int $accountId;

    public function configure(): void
    {
        parent::configure();

        $this->setPrimaryKey('id')
            ->setDefaultSort('created_at', 'desc');

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
        $account = Account::find($this->accountId);

        if (! $account) {
            abort(500);
        }

        $query = AccountBlockModelFactory::create($account)
            ->with(['account', 'toAccount', 'contractMethod', 'token'])
            ->select([
                'hash',
                'account_id',
                'to_account_id',
                'token_id',
                'contract_method_id',
                'paired_account_block_id',
                'amount',
                'block_type',
                'created_at',
            ]);

        if ($account->address !== config('explorer.burn_address')) {
            $query->hideReceiveBlocks();
        }

        return $query;
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
                        'alwaysShort' => true,
                        'copyable' => true,
                        'link' => route('explorer.block.detail', ['hash' => $row->hash]),
                    ])
                ),
            Column::make('Type')
                ->label(
                    fn ($row, Column $column) => $row->display_actual_type
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
                    } else {
                        $label = __('From');
                        $address = view('components.tables.columns.address', [
                            'row' => $row->account,
                            'alwaysShort' => true,
                        ])->render();
                    }

                    return sprintf('%s: %s', $label, $address);
                })->html(),
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
            SelectFilter::make('Type')
                ->options([
                    '' => 'All',
                    'incoming' => 'Incoming',
                    'outgoing' => 'Outgoing',
                ])
                ->filter(function (Builder $builder, string $value) {
                    if ($value === 'incoming') {
                        $builder->where('to_account_id', $this->accountId);
                    } elseif ($value === 'outgoing') {
                        $builder->where('account_id', $this->accountId);
                    }
                }),
            SelectFilter::make('Interactions')
                ->options([
                    '' => 'All',
                    'contracts' => 'Contracts',
                    'accounts' => 'Accounts',
                ])
                ->filter(function (Builder $builder, string $value) {
                    if ($value === 'contracts') {
                        $builder->where(function ($query) {
                            $query->where(function ($query2) {
                                $query2->whereRelation('account', 'is_embedded_contract', '1')
                                    ->where('account_id', '!=', $this->accountId);
                            })->orWhere(function (Builder $query2) {
                                $query2->whereRelation('toAccount', 'is_embedded_contract', '1')
                                    ->where('to_account_id', '!=', $this->accountId);
                            });
                        });
                    } elseif ($value === 'accounts') {
                        $builder->where(function ($query) {
                            $query->where(function ($query2) {
                                $query2->whereRelation('account', 'is_embedded_contract', '0')
                                    ->where('account_id', '!=', $this->accountId);
                            })->orWhere(function (Builder $query2) {
                                $query2->whereRelation('toAccount', 'is_embedded_contract', '0')
                                    ->where('to_account_id', '!=', $this->accountId);
                            });
                        });
                    }
                }),
        ];
    }
}
