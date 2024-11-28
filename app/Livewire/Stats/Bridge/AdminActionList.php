<?php

declare(strict_types=1);

namespace App\Livewire\Stats\Bridge;

use App\Livewire\BaseTable;
use App\Models\Nom\BridgeAdmin;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class AdminActionList extends BaseTable
{
    public ?string $tab = 'all';

    public string $viewMode = 'custom';

    public function configure(): void
    {
        parent::configure();

        $this->setPrimaryKey('id')
            ->setDefaultSort('created_at', 'desc');

        $this->setColumnSelectDisabled();
    }

    public function builder(): Builder
    {
        $bridgeAdmin = BridgeAdmin::getActiveAdmin()->load('account');

        return $bridgeAdmin->account->sentBlocks()
            ->with(['data', 'contractMethod', 'contractMethod.contract'])
            ->select('*')
            ->whereNotNull('contract_method_id')
            ->getQuery();
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->hideIf(true),
            Column::make('Created', 'created_at')
                ->sortable(),
        ];
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Contract')
                ->options([
                    '' => 'All',
                    'bridge' => 'Bridge',
                    'liquidity' => 'Liquidity',
                ])
                ->filter(function (Builder $builder, string $value) {
                    if ($value === 'bridge') {
                        $builder->whereHas('contractMethod', function ($q) {
                            $q->whereRelation('contract', 'name', 'Bridge');
                        });
                    } elseif ($value === 'liquidity') {
                        $builder->whereHas('contractMethod', function ($q) {
                            $q->whereRelation('contract', 'name', 'Liquidity');
                        });
                    }
                }),
        ];
    }

    public function renderCustomView($rows): View
    {
        return view('components.stats.bridge.admin-actions', [
            'actions' => $rows,
        ]);
    }
}
