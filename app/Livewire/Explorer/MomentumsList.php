<?php

declare(strict_types=1);

namespace App\Livewire\Explorer;

use App\Livewire\BaseTable;
use App\Models\Nom\Momentum;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class MomentumsList extends BaseTable
{
    public function configure(): void
    {
        parent::configure();

        $this->setPrimaryKey('id')
            ->setDefaultSort('created_at', 'desc');
    }

    public function builder(): Builder
    {
        $model = new class extends Momentum
        {
            protected $table = 'view_latest_nom_momentums';
        };

        return $model::with('producerPillar')
            ->select([
                'id',
                'producer_pillar_id',
                'height',
                'hash',
                'created_at',
            ])
            ->withCount('accountBlocks');
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
            Column::make('Pillar')
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.pillar-link', [
                        'pillar' => $row->producerPillar,
                    ])
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
}
