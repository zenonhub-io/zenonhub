<?php

declare(strict_types=1);

namespace App\Livewire\Stats\Bridge;

use App\Livewire\BaseTable;
use App\Models\Nom\Pillar;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class OrchestratorList extends BaseTable
{
    public function configure(): void
    {
        parent::configure();

        $this->setPrimaryKey('id')
            ->setDefaultSort('name');

        $this->setColumnSelectDisabled();
    }

    public function builder(): Builder
    {
        return Pillar::with('orchestrator', 'producerAccount')
            ->select('*')
            ->whereHas('orchestrator');
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->hideIf(true),
            Column::make('Name')
                ->sortable()
                ->searchable()
                ->format(
                    fn ($value, $row, Column $column) => view('components.tables.columns.pillar-link', [
                        'pillar' => $row,
                    ])
                ),
            Column::make('Orchestrator')
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.pillar.orchestrator')->withRow($row)
                ),
            Column::make('Address')
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.address', [
                        'row' => $row->producerAccount,
                        'alwaysShort' => true,
                    ])
                ),
            Column::make('Last Active')
//                ->sortable(
//                    fn (Builder $query, string $direction) => $query->orderBy('nom_account.last_active_at', $direction)
//                )
                ->label(
                    fn ($row, Column $column) => $row->producerAccount->last_active_at ? view('components.tables.columns.date', ['date' => $row->producerAccount->last_active_at]) : null
                ),
        ];
    }

    public function filters(): array
    {
        return [];
    }
}
