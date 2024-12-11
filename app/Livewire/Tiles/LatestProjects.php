<?php

declare(strict_types=1);

namespace App\Livewire\Tiles;

use App\Livewire\BaseComponent;
use App\Models\Nom\AcceleratorProject;
use Illuminate\Database\Eloquent\Builder;

class LatestProjects extends BaseComponent
{
    public function render()
    {
        return view('livewire.tiles.latest-projects', [
            'projects' => AcceleratorProject::whereNew()
                ->orWhere(function (Builder $query) {
                    $query->whereAccepted();
                })
                ->latest('updated_at')
                ->limit(4)
                ->get(),
        ]);
    }
}
