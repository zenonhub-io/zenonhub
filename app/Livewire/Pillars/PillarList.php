<?php

declare(strict_types=1);

namespace App\Livewire\Pillars;

use App\Livewire\BaseTable;
use App\Models\Nom\Pillar;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\NumberFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class PillarList extends BaseTable
{
    public ?string $tab = 'all';

    public function configure(): void
    {
        parent::configure();

        $this->setPrimaryKey('id')
            ->setDefaultSort('rank');

        $this->setPerPageAccepted([30, 50, 100, 150])
            ->setPerPage(30);

        $this->setTableRowUrl(fn ($row) => route('pillar.detail', $row->slug))
            ->setTableRowUrlTarget(fn ($row) => 'navigate');
    }

    public function builder(): Builder
    {
        $query = Pillar::with('orchestrator')
            ->withCount('activeDelegators');

        if ($this->tab === 'active') {
            $query->whereProducing();
        }

        if ($this->tab === 'inactive') {
            $query->whereNotProducing();
        }

        if ($this->tab === 'revoked') {
            $query->whereRevoked();
        } else {
            $query->whereActive();
        }

        return $query->orderBy(DB::raw('CASE WHEN revoked_at IS NULL THEN 0 ELSE 1 END'));
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->hideIf(true),
            Column::make('Rank')
                ->sortable()
                ->format(
                    fn ($value, $row, Column $column) => ! $row->revoked_at ? '# ' . $row->rank + 1 : ''
                ),
            Column::make('Name', 'name')
                ->sortable()
                ->searchable(),
            Column::make('Weight')
                ->sortable()
                ->format(
                    fn ($value, $row, Column $column) => $row->display_weight
                ),
            Column::make('Engagement', 'az_engagement')
                ->sortable()
                ->view('tables.columns.pillar.az-engagement'),
            Column::make('Orchestrator')
                ->label(
                    fn ($row, Column $column) => view('tables.columns.pillar.orchestrator')->withRow($row)
                ),
            Column::make('Rewards')
                ->label(
                    fn ($row, Column $column) => $row->momentum_rewards . ' / ' . $row->delegate_rewards
                ),
            Column::make('Momentums')
                ->label(
                    fn ($row, Column $column) => view('tables.columns.pillar.momentums')->withRow($row)
                ),
            Column::make('Delegators')
                ->label(
                    fn ($row, Column $column) => $row->active_delegators_count
                ),
        ];
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Legacy')
                ->options([
                    '' => 'All',
                    '1' => 'Yes',
                    '0' => 'No',
                ])
                ->filter(function (Builder $builder, string $value) {
                    if ($value === '1') {
                        $builder->where('is_legacy', 1);
                    } elseif ($value === '0') {
                        $builder->where('is_legacy', 0);
                    }
                }),
            SelectFilter::make('Orchestrator')
                ->options([
                    '' => 'All',
                    '1' => 'Online',
                    '0' => 'Offline',
                ])
                ->filter(function (Builder $builder, string $value) {
                    if ($value === '1') {
                        $builder->whereRelation('orchestrator', 'is_active', '1');
                    } elseif ($value === '0') {
                        $builder->whereRelation('orchestrator', 'is_active', '0');
                    }
                }),
            NumberFilter::make('AZ Engagement')
                ->config([
                    'minRange' => 0,
                    'maxRange' => 100,
                    'placeholder' => '%',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('az_engagement', '>=', $value);
                }),
        ];
    }
}
