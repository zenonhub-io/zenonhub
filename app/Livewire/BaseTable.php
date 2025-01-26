<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

abstract class BaseTable extends DataTableComponent
{
    public string $viewMode = 'default';

    public function configure(): void
    {
        // $this->setDebugStatus(true);

        $this->setSortingPillsStatus(false)
            ->setFilterPillsStatus(false);

        $this->setPerPageAccepted([10, 25, 50, 100, 150])
            ->setPerPage(25);

        $this->setColumnSelectDisabled();

        //        $this->setTableWrapperAttributes([
        //            'default' => false,
        //            'class' => '',
        //        ]);
        //
        //        $this->setTableAttributes([
        //            'class' => 'w-full overflow-x-auto',
        //        ]);

        //        $this->setTheadAttributes([
        //            'class' => 'sticky-top',
        //        ]);

        if ($this->viewMode === 'spaced') {
            $this->setTableAttributes([
                'class' => 'table-hover table-striped table-nowrap table-spaced',
            ]);
        }

        if ($this->viewMode === 'default') {
            $this->setTableAttributes([
                'class' => 'table-hover table-striped table-nowrap',
            ]);
        }
    }

    public function placeholder(): View
    {
        return view('livewire.placeholders.loading');
    }
}
