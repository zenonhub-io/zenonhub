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
        return Account::select(['nom_accounts.address', 'nom_accounts.name'])
            ->whereRelation('rewards', 'type', AccountRewardTypesEnum::BRIDGE_AFFILIATE)
            ->withSum(['rewards as total_znn' => function ($query) {
                $query->where('token_id', app('znnToken')->id)
                    ->where('type', AccountRewardTypesEnum::BRIDGE_AFFILIATE);
            }], 'amount')
            ->withSum(['rewards as total_qsr' => function ($query) {
                $query->where('token_id', app('qsrToken')->id)
                    ->where('type', AccountRewardTypesEnum::BRIDGE_AFFILIATE);
            }], 'amount')
            ->orderByDesc('total_znn')
            ->groupBy('nom_accounts.address', 'nom_accounts.name');
    }

    public function columns(): array
    {
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
                    fn ($row, Column $column) => app('znnToken')->getFormattedAmount($row->total_znn)
                ),
            Column::make('Total QSR')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderByRaw('total_qsr ' . $direction)
                )
                ->label(
                    fn ($row, Column $column) => app('qsrToken')->getFormattedAmount($row->total_qsr)
                ),
        ];
    }

    public function filters(): array
    {
        return [];
    }
}
