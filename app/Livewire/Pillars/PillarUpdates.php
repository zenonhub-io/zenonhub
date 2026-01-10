<?php

declare(strict_types=1);

namespace App\Livewire\Pillars;

use App\Livewire\BaseTable;
use App\Models\Nom\Pillar;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PillarUpdates extends BaseTable
{
    public int $pillarId;

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
            ->select([
                'id',
                'withdraw_account_id',
                'producer_account_id',
                'momentum_rewards',
                'delegate_rewards',
                'updated_at',
            ])
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
                    fn ($row, Column $column) => view('components.tables.columns.address', [
                        'row' => $row->withdrawAccount,
                        'alwaysShort' => true,
                        'named' => false,
                    ])
                ),
            Column::make('Producer address')
                ->searchable()
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.address', [
                        'row' => $row->producerAccount,
                        'alwaysShort' => true,
                        'named' => false,
                    ])
                ),
            Column::make('Updated', 'updated_at')
                ->sortable()
                ->format(
                    fn ($value, $row, Column $column) => view('components.tables.columns.date', ['date' => $row->updated_at])
                ),
        ];
    }

    public function filters(): array
    {
        return [];
    }
}
