<?php

declare(strict_types=1);

namespace App\Livewire\Pillars;

use App\Livewire\BaseTable;
use App\Models\Nom\Pillar;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PillarMomentums extends BaseTable
{
    public int $pillarId;

    public function configure(): void
    {
        parent::configure();

        $this->setPrimaryKey('id');
    }

    public function builder(): Builder
    {
        return Pillar::find($this->pillarId)?->momentums()
            ->select([
                'id',
                'hash',
                'height',
                'created_at',
            ])
            ->withCount('accountBlocks')
            ->getQuery();
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->searchable(
                    fn (Builder $query, $searchTerm) => $query->where(function ($query) use ($searchTerm) {
                        $query->where('height', $searchTerm)
                            ->orWhere('hash', $searchTerm);
                    })
                )
                ->hideIf(true),
            Column::make('Height', 'height')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderBy('height', $direction)
                )
                ->label(
                    fn ($row, Column $column) => $row->display_height
                ),
            Column::make('Hash', 'hash')
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.hash', [
                        'hash' => $row->hash,
                        'alwaysShort' => true,
                        'copyable' => true,
                        'link' => route('explorer.momentum.detail', ['hash' => $row->hash]),
                    ])
                ),
            Column::make('Age')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderBy('created_at', $direction)
                )
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.date', ['date' => $row->created_at, 'human' => true])
                ),
            Column::make('Blocks')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderBy('account_blocks_count', $direction)
                )
                ->label(
                    fn ($row, Column $column) => number_format($row->account_blocks_count)
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
