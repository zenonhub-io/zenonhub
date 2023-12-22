<?php

namespace App\Http\Livewire;

trait DataTableTrait
{
    public $search;

    public $filters;

    public $sort = 'id';

    public $order = 'desc';

    public $perPage = 50;

    public $simplePaginate = false;

    public $loadResults = false;

    public $namedComponent = false;

    protected $query;

    protected $data = null;

    protected $paginationTheme = 'bootstrap';

    protected $componentName;

    protected function getListeners()
    {
        return ['search', 'export'];
    }

    public function sortBy($field)
    {
        $this->order = $this->sort === $field
            ? $this->reverseSort()
            : 'desc';

        $this->sort = $field;

        $this->resetPage($this->componentName);
    }

    public function reverseSort()
    {
        return $this->order === 'asc'
            ? 'desc'
            : 'asc';
    }

    public function getPerPage()
    {
        return $this->perPage;
    }

    public function setPerPage($number)
    {
        return $this->perPage = (is_numeric($number)
            ? $number
            : 25);
    }

    public function search($query)
    {
        $this->search = $query;
        $this->resetPage($this->componentName);
    }

    public function applyFilters()
    {
        $this->resetPage($this->getComponentName(! $this->namedComponent));
        $this->loadData();
    }

    public function shouldLoadResults()
    {
        $this->loadResults = true;
    }

    protected function loadData()
    {
        $this->componentName = $this->getComponentName(! $this->namedComponent);

        if ($this->loadResults) {
            $this->initQuery();
            $this->filterList();
            $this->sortList();
            $this->getList();
        }
    }

    protected function filterList()
    {
        if (! $this->query) {
            return;
        }

        if ($this->search) {
            $this->query->whereListSearch($this->search);
            $this->resetPage($this->componentName);
        }
    }

    protected function sortList()
    {
        if (! $this->query) {
            return;
        }

        $this->query->orderBy($this->sort, $this->order);
    }

    protected function getList()
    {
        if (! $this->query) {
            return;
        }

        if ($this->simplePaginate) {
            $this->data = $this->query->simplePaginate($this->perPage, ['*'], $this->componentName);
        } else {
            $this->data = $this->query->paginate($this->perPage, ['*'], $this->componentName);
        }
    }

    protected function doExport($export, string $exportName)
    {
        return $export->download($exportName, \Maatwebsite\Excel\Excel::CSV);

        // TODO - revisit when doing account upgrades
        //        if (request()->user() && request()->user()->email_verified_at) {
        //            $this->exported = 'queued';
        //            $exportName = Str::ulid() . '-' . $exportName;
        //            $export->queue("exports/{$exportName}")->chain([
        //                new \App\Jobs\NotifyUserOfCompletedExport(request()->user(), $exportName),
        //            ]);
        //        } else {
        //            $this->exported = 'free';
        //            return $export->download($exportName, \Maatwebsite\Excel\Excel::CSV);
        //        }
    }

    protected function getComponentName($default = true)
    {
        if (! $default) {
            $_class = explode('\\', get_called_class());

            return strtolower(array_reverse($_class)[0]);
        }

        return 'page';
    }
}
