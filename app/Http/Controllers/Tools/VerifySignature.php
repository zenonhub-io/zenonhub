<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\PageController;

class VerifySignature extends PageController
{
    public function show()
    {
        $this->page['meta']['title'] = 'Verify Signed Message';
        $this->page['meta']['description'] = 'Check a signed messages signature is valid for the given address';
        $this->page['data'] = [
            'component' => 'tools.verify-signature',
        ];

        return $this->render('pages/tools');
    }
}
