<?php

declare(strict_types=1);

namespace App\Livewire\Explorer;

use App\Livewire\BaseTable;
use App\Models\Nom\Stake;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class StakingList extends BaseTable
{
    public ?string $tab = 'znn';

    public function configure(): void
    {
        parent::configure();

        $this->setPrimaryKey('id')
            ->setDefaultSort('started_at', 'desc');
    }

    public function builder(): Builder
    {
        $tokenId = app('znnToken')->id;

        if ($this->tab === 'znn-eth-lp' && app('znnEthLpToken')) {
            $tokenId = app('znnEthLpToken')->id;
        }

        return Stake::query()
            ->with([
                'account', 'token', 'accountBlock',
            ])
            ->select([
                'id',
                'token_id',
                'account_id',
                'account_block_id',
                'amount',
                'duration',
                'started_at',
                'ended_at',
            ])
            ->where('token_id', $tokenId)
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
                    fn ($row, Column $column) => $row->token->getFormattedAmount($row->amount) . ' ' . $row->token->symbol
                ),
            Column::make('Address')
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.address', [
                        'row' => $row->account,
                        'alwaysShort' => true,
                    ])
                ),
            Column::make('Started', 'started_at')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderBy('started_at', $direction)
                )
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.date', ['date' => $row->started_at])
                ),
            Column::make('Lockup')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderBy('duration', $direction)
                )
                ->label(
                    fn ($row, Column $column) => $row->display_duration
                ),
            Column::make('Available')
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.date', [
                        'date' => $row->end_date,
                        'tooltip' => false,
                    ])
                ),
            Column::make('Duration')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderBy('started_at', $direction)
                )
                ->label(
                    fn ($row, Column $column) => $row->display_current_duration
                ),
        ];
    }

    public function filters(): array
    {
        return [];
    }
}
