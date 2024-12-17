<?php

declare(strict_types=1);

namespace App\Livewire\Sentinels;

use App\Livewire\BaseTable;
use App\Models\Nom\Sentinel;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class SentinelList extends BaseTable
{
    public ?string $tab = 'all';

    public function configure(): void
    {
        parent::configure();

        $this->setPrimaryKey('id')
            ->setDefaultSort('created_at', 'desc');
    }

    public function builder(): Builder
    {
        return Sentinel::with('owner')
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
                    fn ($row, Column $column) => view('components.tables.columns.address', [
                        'row' => $row->owner,
                        'alwaysShort' => true,
                    ])
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
                    fn ($value, $row, Column $column) => view('components.tables.columns.date', ['date' => $row->created_at])
                ),
        ];
    }

    public function filters(): array
    {
        return [];
    }
}
