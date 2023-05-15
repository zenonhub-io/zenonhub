<?php

namespace App\View\Components;

use Illuminate\View\Component;

class TableSortButton extends Component
{
    public string $sort;

    public string $order;

    public string $check;

    public ?string $title;

    public ?string $tooltip;

    public function __construct(string $sort, string $order, string $check, ?string $title = null, ?string $tooltip = null)
    {
        $this->sort = $sort;
        $this->order = $order;
        $this->check = $check;
        $this->title = ($title ?: \Str::headline($check));
        $this->tooltip = $tooltip;
    }

    public function render()
    {
        return view('components.utilities.table-sort-button');
    }
}
