<?php

declare(strict_types=1);

namespace App\Livewire\Explorer\Account;

use App\Livewire\BaseTable;
use App\Models\Nom\Account;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class TokensList extends BaseTable
{
    public int $accountId;

    public function configure(): void
    {
        parent::configure();

        $this->setPrimaryKey('id')
            ->setDefaultSort('sort_balance', 'desc');
    }

    public function builder(): Builder
    {
        return Account::find($this->accountId)?->tokens()
            ->withPivot(['balance', 'updated_at'])
            ->select('*')
            ->selectRaw('CAST(balance AS INTEGER) / POWER(10, decimals) as sort_balance')
            ->where('balance', '>', '0')
            ->getQuery();
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->hideIf(true),
            Column::make('Token')
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.link', [
                        'link' => route('explorer.token.detail', ['zts' => $row->token_standard]),
                        'text' => $row->symbol,
                    ])
                ),
            Column::make('Amount')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderByRaw('CAST(balance AS INTEGER) / POWER(10, decimals) ' . $direction)
                )
                ->label(
                    fn ($row, Column $column) => $row->balance > 0 ? $row->getFormattedAmount($row->balance) : null
                ),
            Column::make('Share')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderByRaw('CAST(balance AS INTEGER) / POWER(10, decimals) ' . $direction)
                )
                ->label(
                    fn ($row, Column $column) => $row->balance > 0 ? Account::find($this->accountId)?->tokenBalanceShare($row) : null
                ),
        ];
    }

    public function filters(): array
    {
        return [];
    }
}
