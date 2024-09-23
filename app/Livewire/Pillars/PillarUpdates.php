<?php

declare(strict_types=1);

namespace App\Livewire\Pillars;

use App\Livewire\BaseTable;
use App\Models\Nom\Pillar;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PillarUpdates extends BaseTable
{
    public string $pillarId;

    public function configure(): void
    {
        parent::configure();

        $this->setPrimaryKey('id')
            ->setDefaultSort('updated_at', 'desc');
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
