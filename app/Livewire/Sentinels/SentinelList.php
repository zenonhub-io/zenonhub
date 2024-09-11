<?php

declare(strict_types=1);

namespace App\Livewire\Sentinels;

use App\Models\Nom\Sentinel;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class SentinelList extends DataTableComponent
{
    public ?string $tab = 'all';

    public function configure(): void
    {
        //$this->setDebugStatus(true);

        $this->setPrimaryKey('id')
            ->setDefaultSort('created_at', 'desc');

        $this->setSortingPillsStatus(false)
            ->setFilterPillsStatus(false)
            ->setComponentWrapperAttributes([
                'class' => 'table-responsive',
            ])->setTableAttributes([
                'class' => 'table-hover table-striped table-nowrap',
            ]);
    }

    public function builder(): Builder
    {
        return Sentinel::query()
            ->with('owner')
            ->select('*')
            ->whereActive();
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')->hideIf(true),
            Column::make('Address')
                ->searchable(
                    fn (Builder $query, $searchTerm) => $query->where('owner.address', 'like', "%{$searchTerm}%")
                )
                ->label(
                    fn ($row, Column $column) => view('tables.columns.address', ['row' => $row->owner])
                ),
            Column::make('ZNN Balance', 'owner.znn_balance')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderByRaw('CAST(owner.znn_balance AS INTEGER) ' . $direction)
                )
                ->format(
                    fn ($value, $row, Column $column) => $row->owner->display_znn_balance
                ),
            Column::make('QSR Balance', 'owner.qsr_balance')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderByRaw('CAST(owner.qsr_balance AS INTEGER) ' . $direction)
                )
                ->format(
                    fn ($value, $row, Column $column) => $row->owner->display_qsr_balance
                ),
            Column::make('Registered', 'created_at')
                ->sortable()
                ->format(
                    fn ($value, $row, Column $column) => view('tables.columns.date', ['date' => $row->created_at])
                ),
        ];
    }

    public function filters(): array
    {
        return [];
    }
}
