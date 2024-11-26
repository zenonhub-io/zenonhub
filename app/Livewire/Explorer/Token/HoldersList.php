<?php

declare(strict_types=1);

namespace App\Livewire\Explorer\Token;

use App\Livewire\BaseTable;
use App\Models\Nom\Token;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Rappasoft\LaravelLivewireTables\Views\Column;

class HoldersList extends BaseTable
{
    public string $tokenId;

    public function configure(): void
    {
        parent::configure();

        $this->setPrimaryKey('id')
            ->setDefaultSort('formatted_balance', 'desc');
    }

    public function builder(): Builder
    {
        return Token::find($this->tokenId)?->holders()
            ->select('*', DB::raw('CAST(balance AS INTEGER) as formatted_balance'))
            ->wherePivot('balance', '>', 0)
            ->getQuery();
    }

    public function columns(): array
    {
        $token = Token::find($this->tokenId);

        return [
            Column::make('ID', 'id')
                ->hideIf(true),
            Column::make('Address')
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.address')->withRow($row)
                ),
            Column::make('Balance')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderByRaw('CAST(balance AS INTEGER) ' . $direction)
                )
                ->label(
                    fn ($row, Column $column) => $token->getFormattedAmount($row->balance)
                ),
            Column::make('Share')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderByRaw('CAST(balance AS INTEGER) ' . $direction)
                )
                ->label(
                    fn ($row, Column $column) => $row->tokenBalanceShare($token) . '%'
                ),
        ];
    }

    public function filters(): array
    {
        return [];
    }
}
