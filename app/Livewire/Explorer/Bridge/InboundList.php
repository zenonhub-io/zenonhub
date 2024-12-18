<?php

declare(strict_types=1);

namespace App\Livewire\Explorer\Bridge;

use App\Livewire\BaseTable;
use App\Models\Nom\BridgeUnwrap;
use App\Models\Nom\Token;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class InboundList extends BaseTable
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
        return BridgeUnwrap::with('bridgeNetwork', 'token', 'toAccount')
            ->select('*')
            ->whereIsProcessed();
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
                    fn ($row, Column $column) => view('components.tables.columns.hash-link', [
                        'link' => $row->from_address_link,
                        'hash' => $row->from_address,
                        'alwaysShort' => true,
                        'navigate' => false,
                        'newTab' => true,
                    ])
                ),
            Column::make('')
                ->label(fn ($row, Column $column) => view('components.tables.columns.svg')->with([
                    'svg' => $row->redeemed_at ? 'explorer/send' : 'explorer/unreceived',
                    'class' => $row->redeemed_at ? 'text-success' : 'text-danger',
                    'style' => $row->redeemed_at ? 'transform: rotate(90deg);' : null,
                    'tooltip' => ! $row->redeemed_at ? __('Unreceived') : null,
                ])),
            Column::make('To')
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
                    fn ($row, Column $column) => $row->token->getFormattedAmount($row->amount)
                ),
            Column::make('Token')
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.link', [
                        'link' => route('explorer.token.detail', ['zts' => $row->token->token_standard]),
                        'text' => $row->token->symbol,
                    ])
                ),
            Column::make('Type')
                ->label(
                    fn ($row, Column $column) => $row->is_affiliate_reward ? __('Affiliate') : __('Redeem')
                ),
            Column::make('TX Hash', 'transaction_hash')
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.hash-link', [
                        'link' => $row->tx_hash_link,
                        'hash' => $row->transaction_hash,
                        'alwaysShort' => true,
                        'navigate' => false,
                        'newTab' => true,
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
