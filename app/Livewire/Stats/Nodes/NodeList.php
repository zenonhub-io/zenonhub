<?php

declare(strict_types=1);

namespace App\Livewire\Stats\Nodes;

use App\Livewire\BaseTable;
use App\Models\Nom\PublicNode;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class NodeList extends BaseTable
{
    public function configure(): void
    {
        parent::configure();

        $this->setPrimaryKey('id')
            ->setDefaultSort('id');

        $this->setColumnSelectDisabled();
    }

    public function builder(): Builder
    {
        return PublicNode::query()
            ->select('*');
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->hideIf(true),
            Column::make('IP', 'ip')
                ->sortable()
                ->searchable(),
            Column::make('ISP', 'isp')
                ->sortable()
                ->searchable(),
            Column::make('Version')
                ->sortable()
                ->searchable(),
            Column::make('Country')
                ->sortable()
                ->searchable()
                ->format(
                    fn ($value, $row, Column $column) => view('components.tables.columns.country-flag', ['row' => $row])
                ),
            Column::make('City')
                ->sortable()
                ->searchable(),
        ];
    }

    public function filters(): array
    {
        return [];
    }
}
