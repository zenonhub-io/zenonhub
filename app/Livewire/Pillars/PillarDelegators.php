<?php

declare(strict_types=1);

namespace App\Livewire\Pillars;

use App\Livewire\BaseTable;
use App\Models\Nom\Pillar;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PillarDelegators extends BaseTable
{
    public string $pillarId;

    public function configure(): void
    {
        parent::configure();

        $this->setPrimaryKey('id')
            ->setDefaultSort('formatted_znn_balance', 'desc');
    }

    public function builder(): Builder
    {
        return Pillar::find($this->pillarId)?->activeDelegators()
            ->withPivot('started_at')
            ->select(
                'nom_accounts.*',
                'nom_delegations.started_at as delegation_started_at',
                DB::raw('CAST(znn_balance AS INTEGER) as formatted_znn_balance')
            )
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
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.address', [
                        'row' => $row,
                        'alwaysShort' => true,
                    ])
                ),
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
                    fn ($row, Column $column) => view('components.tables.columns.date', ['date' => $row->delegation_started_at])
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
