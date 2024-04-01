<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Domains\Nom\Models\AcceleratorPhase;
use App\Domains\Nom\Models\AcceleratorProject;
use Illuminate\View\Component;

class AzCardHeader extends Component
{
    /**
     * The alert message.
     */
    public AcceleratorProject|AcceleratorPhase $item;

    /**
     * Create the component instance.
     */
    public function __construct(AcceleratorProject|AcceleratorPhase $item)
    {
        $this->item = $item;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.utilities.az-card-header');
    }
}
