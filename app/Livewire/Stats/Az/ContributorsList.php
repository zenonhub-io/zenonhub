<?php

declare(strict_types=1);

namespace App\Livewire\Stats\Az;

use App\Enums\Nom\AcceleratorProjectStatusEnum;
use App\Livewire\BaseTable;
use App\Models\Nom\Account;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class ContributorsList extends BaseTable
{
    public function configure(): void
    {
        parent::configure();

        $this->setPrimaryKey('id')
            ->setDefaultSort('znn_paid', 'desc');

        $this->setColumnSelectDisabled();
    }

    public function builder(): Builder
    {
        return Account::whereHas('projects')
            ->withCount([
                'projects as accepted_projects_count' => fn ($query) => $query->where('status', AcceleratorProjectStatusEnum::ACCEPTED->value),
                'projects as completed_projects_count' => fn ($query) => $query->where('status', AcceleratorProjectStatusEnum::COMPLETE->value),
                'projects as rejected_projects_count' => fn ($query) => $query->where('status', AcceleratorProjectStatusEnum::REJECTED->value),
            ])
            ->withSum(
                'projects as znn_paid', 'znn_paid'
            )
            ->withSum(
                'projects as qsr_paid', 'qsr_paid'
            );

    }

    public function columns(): array
    {
        $znnToken = app('znnToken');
        $qsrToken = app('qsrToken');

        return [
            Column::make('ID', 'id')
                ->hideIf(true),
            Column::make('Account')
                ->searchable()
                ->label(
                    fn ($row, Column $column) => view('components.tables.columns.address', [
                        'row' => $row,
                        'alwaysShort' => true,
                    ])
                ),
            Column::make('ZNN Paid')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderByRaw('znn_paid ' . $direction)
                )
                ->label(
                    fn ($row, Column $column) => $znnToken->getFormattedAmount($row->znn_paid)
                ),
            Column::make('QSR Paid')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderByRaw('qsr_paid ' . $direction)
                )
                ->label(
                    fn ($row, Column $column) => $qsrToken->getFormattedAmount($row->qsr_paid)
                ),
            Column::make('Accepted')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderByRaw('accepted_projects_count ' . $direction)
                )
                ->label(
                    fn ($row, Column $column) => $row->accepted_projects_count
                ),
            Column::make('Completed')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderByRaw('completed_projects_count ' . $direction)
                )
                ->label(
                    fn ($row, Column $column) => $row->completed_projects_count
                ),
            Column::make('Rejected')
                ->sortable(
                    fn (Builder $query, string $direction) => $query->orderByRaw('rejected_projects_count ' . $direction)
                )
                ->label(
                    fn ($row, Column $column) => $row->rejected_projects_count
                ),
        ];
    }

    public function filters(): array
    {
        return [];
    }
}
