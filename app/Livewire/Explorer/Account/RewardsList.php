<?php

declare(strict_types=1);

namespace App\Livewire\Explorer\Account;

use App\Enums\Nom\AccountRewardTypesEnum;
use App\Livewire\BaseTable;
use App\Models\Nom\Account;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class RewardsList extends BaseTable
{
    public int $accountId;

    public function configure(): void
    {
        parent::configure();

        $this->setPrimaryKey('id')
            ->setDefaultSort('created_at', 'desc');
    }

    public function builder(): Builder
    {
        return Account::find($this->accountId)?->rewards()
            ->with(['account', 'token'])
            ->select('*')
            ->getQuery();
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->hideIf(true),
            Column::make('Type')
                ->label(
                    fn ($row, Column $column) => $row->type->label()
                ),
            Column::make('Amount')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderByRaw('CAST(amount AS INTEGER) ' . $direction)
                )
                ->label(
                    fn ($row, Column $column) => $row->token?->getFormattedAmount($row->amount)
                ),
            Column::make('Token')
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.link', [
                        'link' => route('explorer.token.detail', ['zts' => $row->token->token_standard]),
                        'text' => $row->token->symbol,
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
        $options = collect(AccountRewardTypesEnum::cases())
            ->mapWithKeys(fn ($item) => [$item->value => $item->label()])
            ->prepend(__('All'), '')
            ->toArray();

        return [
            SelectFilter::make('Type')
                ->options($options)
                ->filter(function (Builder $builder, string $value) {
                    if (! empty($value)) {
                        $builder->where('type', $value);
                    }
                }),
        ];
    }
}
