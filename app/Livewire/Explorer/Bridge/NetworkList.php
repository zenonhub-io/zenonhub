<?php

declare(strict_types=1);

namespace App\Livewire\Explorer\Bridge;

use App\Livewire\BaseTable;
use App\Models\Nom\BridgeNetwork;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class NetworkList extends BaseTable
{
    public function configure(): void
    {
        parent::configure();

        $this->setPrimaryKey('id')
            ->setDefaultSort('created_at');
    }

    public function builder(): Builder
    {
        return BridgeNetwork::select('*')
            ->withCount('tokens');
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->hideIf(true),
            Column::make('Name')
                ->sortable()
                ->searchable(),
            Column::make('Contract Address', 'contract_address')
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.hash-link', [
                        'link' => $row->explorer_url . '/' . $row->explorer_address_link . '/' . $row->contract_address,
                        'hash' => $row->contract_address,
                        'alwaysShort' => true,
                        'navigate' => false,
                        'newTab' => true,
                    ])
                ),
            Column::make('Wrapped ZNN')
                ->label(
                    fn ($row, Column $column) => app('znnToken')->getFormattedAmount($row->total_znn_held)
                ),
            Column::make('Wrapped QSR')
                ->label(
                    fn ($row, Column $column) => app('qsrToken')->getFormattedAmount($row->total_qsr_held)
                ),
            Column::make('# Tokens')
                ->label(
                    fn ($row, Column $column) => $row->tokens_count
                ),
            Column::make('Chain ID', 'chain_identifier')
                ->sortable()
                ->searchable(),
            Column::make('Network Class', 'network_class')
                ->sortable()
                ->searchable(),

        ];
    }

    public function filters(): array
    {
        return [];
    }
}
