<?php

declare(strict_types=1);

namespace App\Livewire\Explorer\Account;

use App\Enums\Nom\AcceleratorProjectStatusEnum;
use App\Livewire\BaseTable;
use App\Models\Nom\Account;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class ProjectsList extends BaseTable
{
    public string $viewMode = 'custom';

    public int $accountId;

    public function configure(): void
    {
        parent::configure();

        $this->setPrimaryKey('id')
            ->setDefaultSort('created_at');

        $this->setColumnSelectDisabled();

        $this->setPerPageAccepted([6, 12, 24])
            ->setPerPage(12);
    }

    public function builder(): Builder
    {
        return Account::find($this->accountId)?->projects()
            ->with(['phases'])
            ->select([
                'id',
                'hash',
                'name',
                'slug',
                'url',
                'description',
                'status',
                'znn_requested',
                'znn_price',
                'qsr_requested',
                'qsr_price',
                'total_votes',
                'total_yes_votes',
                'total_no_votes',
                'total_abstain_votes',
                'created_at',
            ])
            ->orderByLatest()
            ->getQuery();
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->hideIf(true),
            Column::make('Name')
                ->sortable()
                ->searchable(),
            Column::make('Url')
                ->sortable()
                ->searchable(),
            Column::make('Hash')
                ->sortable()
                ->searchable(),
            Column::make('Description')
                ->sortable()
                ->searchable(),
        ];
    }

    public function filters(): array
    {
        $options = collect(AcceleratorProjectStatusEnum::cases())
            ->mapWithKeys(fn ($item) => [$item->name => $item->label()])
            ->prepend(__('Open'), 'OPEN')
            ->prepend(__('All'), '')
            ->toArray();

        return [
            SelectFilter::make('Status')
                ->options($options)
                ->filter(function (Builder $builder, string $value) {
                    match ($value) {
                        'NEW' => $builder->whereNew(),
                        'OPEN' => $builder->whereOpen(),
                        'ACCEPTED' => $builder->whereAccepted(),
                        'REJECTED' => $builder->whereRejected(),
                        'COMPLETE' => $builder->whereCompleted(),
                    };
                }),
        ];
    }

    public function renderCustomView($rows): View
    {
        return view('components.accelerator-z.grid.project-cards', [
            'projects' => $rows,
        ]);
    }
}
