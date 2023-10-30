<?php

namespace App\Http\Controllers\Services;

use Meta;

class PublicNodes
{
    public function show()
    {
        Meta::title('Public Nodes');

        return view('pages/services/public-nodes');
    }
}
