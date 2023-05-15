<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\PageController;

class ApiPlayground extends PageController
{
    public function show()
    {
        $this->page['meta']['title'] = 'API Playground';
        $this->page['meta']['description'] = 'Explore the public RPC endpoints of the Zenon Network and see the results';
        $this->page['data'] = [
            'component' => 'tools.api-playground',
        ];

        return $this->render('pages/tools');
    }
}
