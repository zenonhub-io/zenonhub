<?php

declare(strict_types=1);

namespace App\Livewire\Pillars;

use App\Models\Nom\Pillar;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PillarUpdates extends DataTableComponent
{
    public string $pillarId;

    public function configure(): void
    {
        //$this->setDebugStatus(true);

        $this->setPrimaryKey('id')
            ->setDefaultSort('updated_at', 'desc');

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
        return Pillar::find($this->pillarId)?->updateHistory()
            ->with(['producerAccount', 'withdrawAccount'])
            ->select('*')
            ->getQuery();
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')->hideIf(true),
            Column::make('Momentum rewards', 'momentum_rewards')->sortable(),
            Column::make('Delegate rewards', 'delegate_rewards')->sortable(),
            Column::make('Rewards address')
                ->searchable()
                ->label(
                    fn ($row, Column $column) => view('tables.columns.address', ['row' => $row->withdrawAccount])
                ),
            Column::make('Producer address')
                ->searchable()
                ->label(
                    fn ($row, Column $column) => view('tables.columns.address', ['row' => $row->producerAccount])
                ),
            Column::make('Updated', 'updated_at')
                ->sortable()
                ->format(
                    fn ($value, $row, Column $column) => view('tables.columns.date', ['date' => $row->updated_at])
                ),
        ];
    }

    public function filters(): array
    {
        return [];
    }
}
