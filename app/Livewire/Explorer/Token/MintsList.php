<?php

declare(strict_types=1);

namespace App\Livewire\Explorer\Token;

use App\Livewire\BaseTable;
use App\Models\Nom\Token;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class MintsList extends BaseTable
{
    public int $tokenId;

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
        return Token::find($this->tokenId)?->mints()
            ->with(['issuer', 'receiver', 'accountBlock'])
            ->select('*')
            ->getQuery();
    }

    public function columns(): array
    {
        $token = Token::find($this->tokenId);

        return [
            Column::make('ID', 'id')
                ->hideIf(true),
            Column::make('Issuer')
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.address', [
                        'row' => $row->issuer,
                        'alwaysShort' => true,
                    ])
                ),
            Column::make('')
                ->label(fn ($row, Column $column) => view('components.tables.columns.svg')->with([
                    'svg' => $row->accountBlock->is_received ? 'explorer/send' : 'explorer/unreceived',
                    'class' => $row->accountBlock->is_received ? 'text-success' : 'text-danger',
                    'style' => $row->accountBlock->is_received ? 'transform: rotate(90deg);' : null,
                    'tooltip' => $row->accountBlock->is_unreceived ? __('Unreceived') : null,
                ])),
            Column::make('	Receiver')
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.address', [
                        'row' => $row->receiver,
                        'alwaysShort' => true,
                    ])
                ),
            Column::make('Amount')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderByRaw('CAST(amount AS SIGNED) ' . $direction)
                )
                ->label(
                    fn ($row, Column $column) => $token->getFormattedAmount($row->amount)
                ),
            Column::make('TX Hash')
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.hash', [
                        'hash' => $row->accountBlock->hash,
                        'alwaysShort' => true,
                        'copyable' => false,
                        'link' => route('explorer.transaction.detail', ['hash' => $row->accountBlock->hash]),
                    ])
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
