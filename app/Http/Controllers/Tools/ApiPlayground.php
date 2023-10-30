<?php

namespace App\Http\Controllers\Tools;

use Meta;

class ApiPlayground
{
    public function show()
    {
        Meta::title('API Playground')
            ->description('Explore the public RPC endpoints of the Zenon Network and see the results');

        return view('pages/tools', [
            'view' => 'tools.api-playground',
        ]);
    }
}
