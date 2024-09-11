<?php

declare(strict_types=1);

namespace App\Livewire\Pillars;

use App\Models\Nom\Pillar;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PillarDelegators extends DataTableComponent
{
    public string $pillarId;

    public function configure(): void
    {
        //$this->setDebugStatus(true);

        $this->setPrimaryKey('id')
            ->setDefaultSort('formatted_znn_balance', 'desc');

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
        return Pillar::find($this->pillarId)?->activeDelegators()
            ->select(
                'nom_accounts.*',
                'nom_delegations.started_at as delegation_started_at',
                DB::raw('CAST(znn_balance AS INTEGER) as formatted_znn_balance')
            )
            ->withPivot('started_at')
            ->getQuery();
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')->hideIf(true),
            Column::make('Address')
                ->searchable(
                    fn (Builder $query, $searchTerm) => $query->where('address', 'like', "%{$searchTerm}%")
                )
                ->view('tables.columns.address'),
            Column::make('Weight')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderByRaw('CAST(znn_balance AS INTEGER) ' . $direction)
                )
                ->label(
                    fn ($row, Column $column) => $row->display_znn_balance
                ),
            Column::make('Share')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderByRaw('CAST(znn_balance AS INTEGER) ' . $direction)
                )
                ->label(
                    fn ($row, Column $column) => $row->display_delegation_percentage_share . '%'
                ),
            Column::make('Started')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderBy('nom_delegations.started_at', $direction)
                )
                ->label(
                    fn ($row, Column $column) => view('tables.columns.date', ['date' => $row->delegation_started_at])
                ),
        ];
    }

    public function filters(): array
    {
        return [];
    }

    //    public function bulkActions(): array
    //    {
    //        return [
    //            'export' => 'Export',
    //        ];
    //    }
    //
    //    public function export()
    //    {
    //        $rows = $this->getSelected();
    //
    //        dd($rows);
    //
    //        $this->clearSelected();
    //
    //        //return Excel::download(new UsersExport($users), 'users.xlsx');
    //    }
}
