<?php

declare(strict_types=1);

namespace App\Http\Controllers\Services;

use Illuminate\Contracts\View\View;
use MetaTags;

class PublicNodesController
{
    public function __invoke(): View
    {
        MetaTags::title('Public Nodes');

        return view('services.public-nodes');
    }
}
