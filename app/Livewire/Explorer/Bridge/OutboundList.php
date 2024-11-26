<?php

declare(strict_types=1);

namespace App\Livewire\Explorer\Bridge;

use App\Livewire\BaseTable;
use App\Models\Nom\BridgeWrap;
use App\Models\Nom\Token;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class OutboundList extends BaseTable
{
    public function configure(): void
    {
        parent::configure();

        $this->setPrimaryKey('id')
            ->setDefaultSort('created_at', 'desc');

        $this->setTdAttributes(function (Column $column, $row, $columnIndex, $rowIndex) {

            if ($column->getTitle() === '') {
                return [
                    'class' => 'py-0 px-0 ps-3 pt-1',
                ];
            }

            return [];
        });
    }

    public function builder(): Builder
    {
        return BridgeWrap::select('*')
            ->with('bridgeNetwork', 'token', 'account', 'accountBlock');
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->hideIf(true),
            Column::make('Network')
                ->label(
                    fn ($row, Column $column) => $row->bridgeNetwork->name
                ),
            Column::make('From')
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.address')
                        ->with(['alwaysShort' => true])
                        ->withRow($row->account)

                ),
            Column::make('')
                ->label(fn ($row, Column $column) => view('components.tables.columns.svg')->with([
                    'svg' => 'explorer/send',
                    'class' => 'text-success',
                    'style' => 'transform: rotate(90deg);',
                ])),
            Column::make('To')
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.hash-link', [
                        'link' => $row->to_address_link,
                        'hash' => $row->to_address,
                        'alwaysShort' => true,
                        'navigate' => false,
                        'newTab' => true,
                    ])
                ),
            Column::make('Amount')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderByRaw('CAST(amount AS INTEGER) ' . $direction)
                )
                ->label(
                    fn ($row, Column $column) => $row->token->getFormattedAmount($row->amount) . ' ' . $row->token->symbol
                ),
            Column::make('TX Hash', 'transaction_hash')
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.hash-link', [
                        'link' => route('explorer.transaction.detail', ['hash' => $row->accountBlock->hash]),
                        'hash' => $row->accountBlock->hash,
                        'alwaysShort' => true,
                    ])
                ),
            Column::make('Timestamp', 'created_at')
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
        $tokens = Token::whereHas('bridgeUnwraps')
            ->orderBy('id')
            ->pluck('name', 'id')
            ->prepend(__('All'), '')
            ->toArray();

        return [
            SelectFilter::make('Token')
                ->options($tokens)
                ->filter(function (Builder $builder, string $value) {
                    if (! empty($value)) {
                        $builder->where('token_id', $value);
                    }
                }),
            SelectFilter::make('Type')
                ->options([
                    '' => 'All',
                    'redeem' => 'Redeem',
                    'affiliate' => 'Affiliate',
                ])
                ->filter(function (Builder $builder, string $value) {
                    if ($value === 'redeem') {
                        $builder->whereNotAffiliateReward();
                    } elseif ($value === 'affiliate') {
                        $builder->whereAffiliateReward();
                    }
                }),
        ];
    }
}
