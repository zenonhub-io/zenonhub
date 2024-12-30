<?php

declare(strict_types=1);

namespace App\Livewire\Stats\Bridge;

use App\Enums\Nom\AccountRewardTypesEnum;
use App\Livewire\BaseTable;
use App\Models\Nom\Account;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class AffiliateList extends BaseTable
{
    public function configure(): void
    {
        parent::configure();

        $this->setPrimaryKey('id')
            ->setDefaultSort('total_znn', 'desc');

        $this->setColumnSelectDisabled();
    }

    public function builder(): Builder
    {
        $znnToken = app('znnToken');
        $qsrToken = app('qsrToken');

        return Account::select(['nom_accounts.address', 'nom_accounts.name'])
            ->withSum(['rewards as total_znn' => function ($query) use ($znnToken) {
                $query->where('token_id', $znnToken->id)
                    ->where('type', AccountRewardTypesEnum::BRIDGE_AFFILIATE);
            }], 'amount')
            ->withSum(['rewards as total_qsr' => function ($query) use ($qsrToken) {
                $query->where('token_id', $qsrToken->id)
                    ->where('type', AccountRewardTypesEnum::BRIDGE_AFFILIATE);
            }], 'amount')
            ->orderByDesc('total_znn')
            ->groupBy('nom_accounts.address', 'nom_accounts.name');
    }

    public function columns(): array
    {
        $znnToken = app('znnToken');
        $qsrToken = app('qsrToken');

        return [
            Column::make('ID', 'id')
                ->hideIf(true),
            Column::make('Address')
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.address', [
                        'row' => $row,
                        'alwaysShort' => true,
                    ])
                ),
            Column::make('Total ZNN')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderByRaw('total_znn ' . $direction)
                )
                ->label(
                    fn ($row, Column $column) => $znnToken->getFormattedAmount($row->total_znn)
                ),
            Column::make('Total QSR')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderByRaw('total_qsr ' . $direction)
                )
                ->label(
                    fn ($row, Column $column) => $qsrToken->getFormattedAmount($row->total_qsr)
                ),
        ];
    }

    public function filters(): array
    {
        return [];
    }
}
