<?php

declare(strict_types=1);

namespace App\Livewire\Explorer;

use App\Livewire\BaseTable;
use App\Models\Nom\Token;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class TokenList extends BaseTable
{
    public ?string $tab = 'all';

    public function configure(): void
    {
        parent::configure();

        $this->setPrimaryKey('id')
            ->setDefaultSort('holders_count', 'desc');

        //        $this->setTableRowUrl(fn ($row) => route('explorer.token.detail', $row->token_standard))
        //            ->setTableRowUrlTarget(fn ($row) => 'navigate');
    }

    public function builder(): Builder
    {
        $query = Token::with('owner')
            ->select('*')
            ->withCount(['holders as holders_count' => fn ($query) => $query->where('balance', '>', '0')]);

        if ($this->tab === 'network') {
            $query->whereRelation('owner', 'is_embedded_contract', '1');
        }

        if ($this->tab === 'user') {
            $query->whereRelation('owner', 'is_embedded_contract', '0');
        }

        return $query;
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->searchable(
                    fn (Builder $query, $searchTerm) => $query->where(function ($query) use ($searchTerm) {
                        $query->where('name', 'like', '%' . $searchTerm . '%')
                            ->orWhere('symbol', 'like', '%' . $searchTerm . '%')
                            ->orWhere('token_standard', $searchTerm);
                    })
                )
                ->hideIf(true),
            Column::make('Name')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderBy('name', $direction)
                )
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.link', [
                        'link' => route('explorer.token.detail', ['zts' => $row->token_standard]),
                        'text' => $row->name,
                    ])
                )->html(),
            Column::make('	Holders')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderBy('holders_count', $direction)
                )
                ->label(
                    fn ($row, Column $column) => number_format($row->holders_count)
                ),
            Column::make('Total Supply')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderByRaw('total_supply / POWER(10, decimals) ' . $direction)
                )
                ->label(
                    fn ($row, Column $column) => $row->getFormattedAmount($row->total_supply)
                ),
            Column::make('Token Standard')
                ->label(
                    fn ($row, Column $column) => $row->token_standard
                ),
            Column::make('Owner')
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.address', [
                        'row' => $row->owner,
                        'alwaysShort' => true,
                    ])
                ),
            Column::make('Created')
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
