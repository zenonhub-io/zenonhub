<?php

declare(strict_types=1);

namespace App\Livewire\Explorer;

use App\Livewire\BaseTable;
use App\Models\Nom\Momentum;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class MomentumList extends BaseTable
{
    public function configure(): void
    {
        parent::configure();

        $this->setPrimaryKey('id')
            ->setDefaultSort('created_at', 'desc');

        //        $this->setTableRowUrl(fn ($row) => route('explorer.momentum.detail', $row->hash))
        //            ->setTableRowUrlTarget(fn ($row) => 'navigate');
    }

    public function builder(): Builder
    {
        $model = new class extends Momentum
        {
            protected $table = 'view_latest_nom_momentums';
        };

        return $model::select('*')
            ->with('producerPillar')
            ->withCount('accountBlocks');
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->hideIf(true),
            Column::make('Height', 'height')
                ->searchable()
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderBy('height', $direction)
                )
                ->label(
                    fn ($row, Column $column) => $row->display_height
                ),
            Column::make('Hash', 'hash')
                ->searchable()
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.hash-link', [
                        'link' => route('explorer.momentum.detail', ['hash' => $row->hash]),
                        'hash' => $row->hash,
                        'alwaysShort' => true,
                    ])
                ),
            Column::make('Age')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderBy('created_at', $direction)
                )
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.date', ['date' => $row->created_at, 'human' => true])
                ),
            Column::make('Pillar')
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.pillar-link')->withRow($row->producerPillar)
                ),
            Column::make('Transactions')
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
}
