<?php

declare(strict_types=1);

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;

abstract class BaseTable extends DataTableComponent
{
    public string $viewMode = 'compact';

    public function configure(): void
    {
        //$this->setDebugStatus(true);

        $this->setSortingPillsStatus(false)
            ->setFilterPillsStatus(false);

        $this->setComponentWrapperAttributes([
            'class' => 'table-responsive',
        ]);

        if ($this->viewMode === 'spaced') {
            $this->setTableAttributes([
                'class' => 'table-hover table-striped table-nowrap table-spaced',
            ]);
        }

        if ($this->viewMode === 'compact') {
            $this->setTableAttributes([
                'class' => 'table-hover table-striped table-nowrap',
            ]);
        }
    }
}
